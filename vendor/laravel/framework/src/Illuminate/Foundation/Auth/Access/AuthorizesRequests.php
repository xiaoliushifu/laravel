<?php

namespace Illuminate\Foundation\Auth\Access;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Auth\Access\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait AuthorizesRequests
{
    /**
     * Authorize a given action against a set of arguments.
     *
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return \Illuminate\Auth\Access\Response
     * 如果authorize方法判断为当前用户授权该动作失败，将会抛出HttpException 异常并生成带403 Not Authorized状态码的HTTP响应。
     * 正如你所看到的，authorize方法是一个授权动作、抛出异常的便捷方法。
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function authorize($ability, $arguments = [])
    {
        list($ability, $arguments) = $this->parseAbilityAndArguments($ability, $arguments);

        return $this->authorizeAtGate(app(Gate::class), $ability, $arguments);
    }

    /**
     * Authorize a given action for a user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|mixed  $user
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return \Illuminate\Auth\Access\Response
     *authorize是对当前登录用户（session）做权限验证，而authorizeForUser可以为非当前用户执行权限验证
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function authorizeForUser($user, $ability, $arguments = [])
    {
        list($ability, $arguments) = $this->parseAbilityAndArguments($ability, $arguments);

        $gate = app(Gate::class)->forUser($user);

        return $this->authorizeAtGate($gate, $ability, $arguments);
    }

    /**
     * Authorize the request at the given gate.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function authorizeAtGate(Gate $gate, $ability, $arguments)
    {
        try {
            return $gate->authorize($ability, $arguments);
        } catch (UnauthorizedException $e) {
            throw $this->createGateUnauthorizedException(
                $ability, $arguments, $e->getMessage(), $e
            );
        }
    }

    /**
     * Guesses the ability's name if it wasn't provided.
     *
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return array
     */
    protected function parseAbilityAndArguments($ability, $arguments)
    {
        if (is_string($ability)) {
            return [$ability, $arguments];
        }

        return [debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'], $ability];
    }

    /**
     * Throw an unauthorized exception based on gate results.
     *
     * @param  string  $ability
     * @param  mixed|array  $arguments
     * @param  string  $message
     * @param  \Exception  $previousException
     * @return \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function createGateUnauthorizedException($ability, $arguments, $message = 'This action is unauthorized.', $previousException = null)
    {
        return new HttpException(403, $message, $previousException);
    }
}
