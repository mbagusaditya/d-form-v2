import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import google723582 from './google'
import githubF226c8 from './github'
/**
* @see \App\Http\Controllers\Auth\LoginController::login
* @see Http/Controllers/Auth/LoginController.php:13
* @route '/auth/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '/auth/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\LoginController::login
* @see Http/Controllers/Auth/LoginController.php:13
* @route '/auth/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\LoginController::login
* @see Http/Controllers/Auth/LoginController.php:13
* @route '/auth/login'
*/
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\LoginController::login
* @see Http/Controllers/Auth/LoginController.php:13
* @route '/auth/login'
*/
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\LoginController::login
* @see Http/Controllers/Auth/LoginController.php:19
* @route '/auth/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: login.url(options),
    method: 'post',
})

login.definition = {
    methods: ["post"],
    url: '/auth/login',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\LoginController::login
* @see Http/Controllers/Auth/LoginController.php:19
* @route '/auth/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\LoginController::login
* @see Http/Controllers/Auth/LoginController.php:19
* @route '/auth/login'
*/
login.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: login.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\RegisterController::register
* @see Http/Controllers/Auth/RegisterController.php:15
* @route '/auth/register'
*/
export const register = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

register.definition = {
    methods: ["get","head"],
    url: '/auth/register',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\RegisterController::register
* @see Http/Controllers/Auth/RegisterController.php:15
* @route '/auth/register'
*/
register.url = (options?: RouteQueryOptions) => {
    return register.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\RegisterController::register
* @see Http/Controllers/Auth/RegisterController.php:15
* @route '/auth/register'
*/
register.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\RegisterController::register
* @see Http/Controllers/Auth/RegisterController.php:15
* @route '/auth/register'
*/
register.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: register.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\RegisterController::register
* @see Http/Controllers/Auth/RegisterController.php:21
* @route '/auth/register'
*/
export const register = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: register.url(options),
    method: 'post',
})

register.definition = {
    methods: ["post"],
    url: '/auth/register',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\RegisterController::register
* @see Http/Controllers/Auth/RegisterController.php:21
* @route '/auth/register'
*/
register.url = (options?: RouteQueryOptions) => {
    return register.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\RegisterController::register
* @see Http/Controllers/Auth/RegisterController.php:21
* @route '/auth/register'
*/
register.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: register.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\OAuthController::google
* @see Http/Controllers/Auth/OAuthController.php:18
* @route '/auth/google'
*/
export const google = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: google.url(options),
    method: 'get',
})

google.definition = {
    methods: ["get","head"],
    url: '/auth/google',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\OAuthController::google
* @see Http/Controllers/Auth/OAuthController.php:18
* @route '/auth/google'
*/
google.url = (options?: RouteQueryOptions) => {
    return google.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\OAuthController::google
* @see Http/Controllers/Auth/OAuthController.php:18
* @route '/auth/google'
*/
google.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: google.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\OAuthController::google
* @see Http/Controllers/Auth/OAuthController.php:18
* @route '/auth/google'
*/
google.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: google.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\OAuthController::github
* @see Http/Controllers/Auth/OAuthController.php:96
* @route '/auth/github'
*/
export const github = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: github.url(options),
    method: 'get',
})

github.definition = {
    methods: ["get","head"],
    url: '/auth/github',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\OAuthController::github
* @see Http/Controllers/Auth/OAuthController.php:96
* @route '/auth/github'
*/
github.url = (options?: RouteQueryOptions) => {
    return github.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\OAuthController::github
* @see Http/Controllers/Auth/OAuthController.php:96
* @route '/auth/github'
*/
github.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: github.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\OAuthController::github
* @see Http/Controllers/Auth/OAuthController.php:96
* @route '/auth/github'
*/
github.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: github.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\LogoutController::__invoke
* @see Http/Controllers/Auth/LogoutController.php:11
* @route '/auth/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '/auth/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\LogoutController::__invoke
* @see Http/Controllers/Auth/LogoutController.php:11
* @route '/auth/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\LogoutController::__invoke
* @see Http/Controllers/Auth/LogoutController.php:11
* @route '/auth/logout'
*/
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

const auth = {
    login: Object.assign(login, login),
    register: Object.assign(register, register),
    google: Object.assign(google, google723582),
    github: Object.assign(github, githubF226c8),
    logout: Object.assign(logout, logout),
}

export default auth