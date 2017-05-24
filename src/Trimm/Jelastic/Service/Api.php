<?php
/**
 * Created by PhpStorm.
 * User: mwienk
 * Date: 1/26/17
 * Time: 1:55 PM
 */

namespace Trimm\Jelastic\Service;

use Unirest\Request;

class Api
{
    const APP_ID = '1dd8d191d38fff45e62564fcf67fdcd6';
    const API_VERSION = '/1.0';

    protected $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache->getAdapter();
    }

    public function getEnvs()
    {
        return $this->get('/environment/control/rest/getenvs');
    }

    public function getEnvInfo($envName)
    {
        return $this->get('/environment/control/rest/getenvinfo', array(
            'envname' => $envName
        ));
    }

    public function bindExtDomain($envName, $extDomain)
    {
        return $this->get('/environment/binder/rest/bindextdomain', array(
            'envname' => $envName,
            'extdomain' => $extDomain
        ));
    }

    public function execCommandById($envName, $nodeId, $commandList = array(), $sayYes = true)
    {
        return $this->get('/environment/control/rest/execcmdbyid', array(
            'envname' => $envName,
            'nodeid' => $nodeId,
            'commandList' => json_encode($commandList),
            'sayyes' => $sayYes
        ));
    }

    public function cloneEnv($appid, $domain)
    {
        return $this->get('/environment/control/rest/cloneenv', array(
            'appid' => $appid,
            'domain' => $domain
        ));
    }

    public function getAppIdForDomain($domain)
    {
        $environments = $this->getEnvs();
        $infos = $environments->infos;
        foreach ($infos as $info) {
            if ($info->env->domain === $domain) {
                return $info->env->appid;
            }
        }
    }

    /**
     * Sign in the user (resets credentials if provided)
     *
     * @param null $username
     * @param null $password
     * @param null $platformUrl
     * @return string
     * @throws \Exception
     */
    public function signin($username = null, $password = null, $platformUrl = null)
    {
        $cachedUsername = $this->cache->getItem('credentials.username');
        $cachedPassword = $this->cache->getItem('credentials.password');
        $cachedPlatformUrl = $this->cache->getItem('endpoint');

        if($username != null) {
            $cachedUsername->set($username);
            $this->cache->save($cachedUsername);
        }

        if($password != null) {
            $cachedPassword->set($password);
            $this->cache->save($cachedPassword);
        }

        if($platformUrl != null) {
            $cachedPlatformUrl->set(rtrim($platformUrl, '/'));
            $this->cache->save($cachedPlatformUrl);
        }

        $result = $this->get('/users/authentication/rest/signin', array(
            'login' => $cachedUsername->get(),
            'password' => $cachedPassword->get()
        ));

        if (isset($result->error)) {
           throw new \Exception("Couldn't login, check your credentials.");
        }

        $cachedSession = $this->cache->getItem('sessionid');
        $cachedSession->set($result->session);
        $this->cache->save($cachedSession);

        return 'User ' . $result->uid .  ' (' . $result->email . ') logged in';
    }

    /**
     * @param $path
     * @param array $params
     * @return mixed
     */
    protected function get($path, $params = array())
    {
        $session = $this->cache->getItem('sessionid');
        if ($session->isHit()) {
            $params['session'] = $session->get();
        }
        if (!array_key_exists('appid', $params)) {
            $params['appid'] = self::APP_ID;
        }
        $platformUrl = $this->cache->getItem('endpoint');
        $response = Request::get($platformUrl->get() . self::API_VERSION . $path, [], $params);
        return $response->body;
    }
}