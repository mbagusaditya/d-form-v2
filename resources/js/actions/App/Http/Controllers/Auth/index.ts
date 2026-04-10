import LoginController from './LoginController'
import RegisterController from './RegisterController'
import OAuthController from './OAuthController'
import LogoutController from './LogoutController'

const Auth = {
    LoginController: Object.assign(LoginController, LoginController),
    RegisterController: Object.assign(RegisterController, RegisterController),
    OAuthController: Object.assign(OAuthController, OAuthController),
    LogoutController: Object.assign(LogoutController, LogoutController),
}

export default Auth