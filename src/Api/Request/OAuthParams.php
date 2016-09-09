<?php
namespace Athena\Api\Request;

use Athena\Athena;

class OAuthParams
{

    private $basicAuthUser;
    private $basicAuthPass;
    private $OAuthUser;
    private $OAuthPass;
    private $grantType;
    private $scope;
    private $oAuthEndpoint;

    /**
     * OAuthParams constructor.
     * @param string $OAuthEndpoint
     * @param $OAuthUser
     * @param $OAuthPass
     * @param $scope
     * @param $grantType
     * @param $basicAuthUser
     * @param $basicAuthPass
     */
    public function __construct($OAuthUser, $OAuthPass, $scope, $grantType, $OAuthEndpoint = '/api/v2/oauth/token/', $basicAuthUser = null, $basicAuthPass = null)
    {
        $this->OAuthUser = $OAuthUser;
        $this->OAuthPass = $OAuthPass;
        $this->scope = $scope;
        $this->grantType = $grantType;
        $this->OAuthEndpointl = $OAuthEndpoint;
        $this->basicAuthUser = $basicAuthUser;
        $this->basicAuthPass = $basicAuthPass;
    }

    /**
     * @return string
     */
    private function getOAuthEndpoint()
    {
        return $this->oAuthEndpoint;
    }

    /**
     * @param string $oAuthEndpoint
     * @return OAuthParams
     */
    public function setOAuthEndpoint($oAuthEndpoint)
    {
        $this->oAuthEndpoint = $oAuthEndpoint;
        return $this;
    }

    /**
     * @param mixed $basicAuthUser
     * @return OAuthParams
     */
    public function setBasicAuthUser($basicAuthUser)
    {
        $this->basicAuthUser = $basicAuthUser;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBasicAuthUser()
    {
        return $this->basicAuthUser;
    }

    /**
     * @param mixed $basicAuthPass
     * @return OAuthParams
     */
    public function setBasicAuthPass($basicAuthPass)
    {
        $this->basicAuthPass = $basicAuthPass;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBasicAuthPass()
    {
        return $this->basicAuthPass;
    }

    /**
     * @param mixed $OAuthUser
     * @return OAuthParams
     */
    public function setOAuthUser($OAuthUser)
    {
        $this->OAuthUser = $OAuthUser;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOAuthUser()
    {
        return $this->OAuthUser;
    }

    /**
     * @param mixed $OAuthPass
     * @return OAuthParams
     */
    public function setOAuthPass($OAuthPass)
    {
        $this->OAuthPass = $OAuthPass;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOAuthPass()
    {
        return $this->OAuthPass;
    }

    /**
     * @param mixed $grantType
     * @return OAuthParams
     */
    public function setGrantType($grantType)
    {
        $this->grantType = $grantType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGrantType()
    {
        return $this->grantType;
    }

    /**
     * @param mixed $scope
     * @return OAuthParams
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }

    public function getToken()
    {
        $OAuthRequest = Athena::api()
            ->post($this->getOAuthEndpoint())
            ->withFormParameter('grant_type', $this->getGrantType())
            ->withFormParameter('scope', $this->getScope())
            ->withFormParameter('username', $this->getOAuthUser())
            ->withFormParameter('password', $this->getOAuthPass());

        if (!empty($this->getBasicAuthUser())) {
            $OAuthRequest->withBasicAuth($this->getBasicAuthUser(), $this->getBasicAuthPass());
        }

        return $OAuthRequest
            ->then()
            ->assertThat()
            ->statusCodeIs(200)
            ->responseIsJson()
            ->retrieve()
            ->fromJson()["access_token"];
    }
}

