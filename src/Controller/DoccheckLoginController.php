<?php

/**
 * Doccheck Login Bundle for Contao Open Source CMS
 * @copyright (c) 2021 Tastaturberuf
 * @author    Daniel Jahnsmüller <https://tastaturberuf.de>
 * @license   LGPL-3.0-or-later
 */


declare(strict_types=1);


namespace Tastaturberuf\ContaoDoccheckLoginBundle\Controller;


use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Environment;
use Contao\FrontendUser;
use Contao\Input;
use Contao\MemberModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;


class DoccheckLoginController extends AbstractFrontendModuleController
{

    public const NAME = 'doccheck_login';

    private const URL     = 'https://login.doccheck.com/code/%s/%s/%s/';
    private const REFERER = 'https://login.doccheck.com/';


    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AuthenticationSuccessHandlerInterface
     */
    private $authenticationSuccessHandler;


    public function __construct(
        UserProviderInterface $userProvider,
        UserCheckerInterface  $userChecker,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        AuthenticationSuccessHandlerInterface $authenticationSuccessHandler
    )
    {
        $this->userProvider = $userProvider;
        $this->userChecker  = $userChecker;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
    }


    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        if ( $this->isValidDoccheckRequest() )
        {
            if ( null === $memberModel = MemberModel::findOneBy('id', $model->doccheck_user) )
            {
                throw new AccessDeniedException('Cant find user with ID: '.$memberModel->doccheck_user);
            }

            $this->loginUser($memberModel->username, $request);

            throw new RedirectResponseException(PageModel::findByPk($model->doccheck_jump_to)->getFrontendUrl());
        }

        $template->url = sprintf(self::URL,
            $model->doccheck_login_id,
            $model->doccheck_language,
            $model->doccheck_style
        );

        return $template->getResponse();
    }


    private function isValidDoccheckRequest(): bool
    {
        return
            self::REFERER === Environment::get('http_referer')
            && 1 == Input::get('dc')
            && Input::get('dc_timestamp') > (time() - 10)
            && Input::get('dc_timestamp') <= time()
        ;
    }


    /**
     * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
     * @author  Daniel Jahnsmüller <https://tastaturberuf.de>
     * @see     https://github.com/richardhj/contao-email-token-login/blob/master/src/Controller/TokenLogin.php#L134
     */
    private function loginUser(string $username, Request $request): void
    {
        try {
            $user = $this->userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $exception) {
            throw new PageNotFoundException('We don\'t know who you are :-(');
        }

        if (!$user instanceof FrontendUser) {
            throw new AccessDeniedException('Not a frontend user');
        }

        try {
            $this->userChecker->checkPreAuth($user);
            $this->userChecker->checkPostAuth($user);
        } catch (AccountStatusException $e) {
            // i.e. account disabled
            throw new AccessDeniedException('Authentication checks failed');
        }

        $usernamePasswordToken = new UsernamePasswordToken($user, null, 'frontend', $user->getRoles());
        $this->tokenStorage->setToken($usernamePasswordToken);
        $event = new InteractiveLoginEvent($request, $usernamePasswordToken);
        $this->eventDispatcher->dispatch($event);
        $this->logger->log(
            LogLevel::INFO,
            'User "' . $username . '" was logged in over Doccheck',
            ['contao' => new ContaoContext(__METHOD__, TL_ACCESS)]
        );

        $this->authenticationSuccessHandler->onAuthenticationSuccess($request, $usernamePasswordToken);
    }

}
