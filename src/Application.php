<?php
/*
 * intelligent-Software
 * Take artificial intelligence with you everywhere.
 *
 * Copyright (C) iSoftware
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

 namespace iSoftware\Engine;

use iSoftware\Engine as Engine;
use iSoftware\Engine\Exception as Exception;
use iSoftware\Engine\Parameters as Parameters;
use \HttpClient as requestClient;

/**
  * Application
  *
  * @author  iSoftware <iSoftware.NGO@gmail.com>
  * @license http://www.gnu.org/licenses/agpl-3.0
  * @link    https://github.com/i-Software/Enigne
  */
abstract class Application extends Engine
{

  /**
   * @Inject
   * @var requestClient
   */
  protected $requestClient;

  /**
   * @return string
   */
   public function getHost()
   {
       return $this->Getter('host');
   }

   /**
    * @param string $host
    * @return string
    */
   public function setHost($host)
   {
       $this->Setter('host', $host);
   }

   /**
    * @return string
    */
   public function getPath()
   {
       return $this->Getter('path');
   }

  /**
    * @param string $path
    * @return string
   */
   public function setPath($path)
   {
       $this->Setter('path', $path);
   }

  /**
   * @return string
   */
   public function getMethod()
   {
       return strtolower($this->Getter('method'));
   }

   /**
    * @param string $method
    */
    public function setMethod($method)
    {
        $this->Setter('method', $method);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeader($name)
    {
        return $this->requestClient->getHeader($name);
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->Getter('parameters');
    }

    /**
     * @param array $parameters
     * @return array
     */
    public function setParameters(array $parameters)
    {
        $this->Setter('parameters', $parameters);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->requestClient->getContent();
    }

    /**
    * @param array $clientParameters
    * @return array
    */
    public function requestParameters(array $clientParameters)
    {
        $this->requestParameters = new Parameters($this->getParameters(), $clientParameters);
        return $this->requestParameters->toArray();
    }

    /**
    * @return []
    */
    protected function beforeSend()
    {
    }


    /**
    * onComplete
    */
    protected function onComplete()
    {
        if ($this->requestClient->getError()) {
            $this->onError($this->requestClient->getError(), "request");
        } else {
            $this->onSucceed();
        }
    }

    /**
    * onSucceed
    */
    protected function onSucceed()
    {
    }
    /**
    * @param string $message
    * @param string $type
    * @throws Exception
    */
    protected function onError($message, $type)
    {
        $this->error("ApplicationExcpeption::$type", $message);
    }

    /**
    * request
    */
    protected function request()
    {
        $this->requestClient = new requestClient($this->getHost());
        $this->beforeSend();
        $getMethod = $this->getMethod();
        $methodsList = array_flip(get_class_methods($this->requestClient));
        if (isset($methodsList[$getMethod])) {
            $Parameters = $this->requestParameters($this->Getter('input')['parameters']);
            $this->requestClient->$getMethod($this->getPath(), $Parameters);
            $this->onComplete();
        } else {
            $this->error('ApplicationException::request', '<'.$this->getMethod().'> is not callable method');
        }
    }
}
