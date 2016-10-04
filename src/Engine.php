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

namespace iSoftware;

use iSoftware\Engine\Exception as Exception;

/**
  * Engine
  *
  * @author  iSoftware <iSoftware.NGO@gmail.com>
  * @license http://www.gnu.org/licenses/agpl-3.0
  * @link    https://github.com/i-Software/Enigne
  */

class Engine
{

 /**
  * @var array
  */
  protected $Base = array(
   "groups" => array(
     "NLP",
     "TTS",
     "ImageRecognition",
   ));

   /**
   * @var array
   */
   protected $application = array(
     "group" => "",
     "config" => "",
     "parameters" => "",
     "method" => "GET",
     "host" => "",
     "path" => "",
     "callback" => ""
   );

  /**
   * @var array
   */
   public $input,$output;

  /**
  * Getter
  * @param string $Getter
  * @return []
  */
  public function Getter($Getter)
  {
      $Getter = trim(strtolower($Getter));
      switch ($Getter) {
        case 'input':
          $return = $this->input;
        break;
        case 'output':
          $return = $this->output;
        break;
        case 'groups':
          $return = $this->Base['groups'];
        break;
        case 'app':
          $return = $this->application;
        break;
        case 'group':
          if (!in_array($this->application['group'], $this->Getter('groups'))) {
              $this->error("ApplicationException", "invalid group, ".$this->application['group'].' is not in enumeration [' . implode(', ', $this->Getter('groups')). ']');
          }
          $return = $this->application['group'];
        break;
        case 'config':
          $return = $this->application['config'];
        break;
        case 'parameters':
          $return = $this->application['parameters'];
        break;
        case 'method':
          $methods = array("GET", "POST", "PUT", "DEL"."ETE");
          if (!in_array(strtoupper($this->application['method']), $methods)) {
              $this->error("ApplicationException", "invalid request method, ".$this->application['method'].' is not in enumeration [' . implode(', ', $methods). ']');
          }
          $return = $this->application['method'];
        break;
        case 'host':
          if (!$this->isValidHost($this->application['host'])) {
              $this->error("ApplicationException", "invalid request host, " .$this->application['host']." is not a valid hostname");
          }
          $return = $this->application['host'];
        break;
        case 'path':
          if (!preg_match('%^/(?!.*\/$)(?!.*[\/]{2,})(?!.*\?.*\?)(?!.*\.\/).*%im', $this->application['path'])) {
              $this->error("ApplicationException", "invalid request path, ".$this->application['path']." is not a valid path");
          }
          $return = $this->application['path'];
        break;
        case 'callback':
          $callback = $this->application['callback'];
          $functionsList = array_flip(get_class_methods($this));
          if (!isset($functionsList[$callback])) {
              $this->error("ApplicationException", "application callback is not a callable function");
          }
          $return = $this->application['callback'];
        break;
      }
      return (isset($return))?$return:null;
  }

  /**
  * Setter
  * @param string $Setter
  * @param string $value
  * @param sting $key
  */
  public function Setter($Setter, $value, $key = "")
  {
      $Setter = trim(strtolower($Setter));

      switch ($Setter) {
      case 'input':
        $this->input[$key] = $value;
      break;
      case 'output':
        $this->output[$key] = $value;
      break;
      case 'group':
        if (!in_array($value, $this->Getter('groups'))) {
            $this->error("ApplicationException", "invalid group, $value ".' is not in enumeration [' . implode(', ', $this->Getter('groups')). ']');
        }
        $this->application['group'] = $value;
      break;
      case 'config':
        $this->application['config'] = $value;
      break;
      case 'parameters':
        $this->application['parameters'] = $value;
      break;
      case 'method':
        $methods = array("GET", "POST", "PUT", "DEL"."ETE");
        if (!in_array(strtoupper($value), $methods)) {
            $this->error("ApplicationException", "invalid request method, $value ".' is not in enumeration [' . implode(', ', $methods). ']');
        }
        $this->application['method'] = strtoupper($value);
      break;
      case 'host':
        if (!$this->isValidHost($value)) {
            $this->error("ApplicationException", "invalid request host, $value is not a valid hostname");
        }
        $this->application['host'] = $value;
      break;
      case 'path':
        if (!preg_match('%^/(?!.*\/$)(?!.*[\/]{2,})(?!.*\?.*\?)(?!.*\.\/).*%im', $value)) {
            $this->error("ApplicationException", "invalid request path, $value is not a valid path");
        }
        $this->application['path'] = $value;
      break;
      case 'callback':
        $callback = $value;
        $functionsList = array_flip(get_class_methods($this));
        if (!isset($functionsList[$callback])) {
            $this->error("ApplicationException", is_callable($this->$callback) ."application callback is not a callable function");
        }
        $this->application['callback'] = $value;
      break;
    }
  }

  /**
  * @param string $url
  * @return Boolean
  */
  protected function isValidURL($url)
  {
      return (bool)parse_url($url);
  }
  /**
  * @param string $domain
  */
  protected function isValidHost($domain)
  {
      return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain) //valid chars check
              && preg_match("/^.{1,253}$/", $domain) //overall length check
              && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain)); //length of each label
  }
  /**
  * trigger error
  * @param string $error
  * @param string $errorSummary
  * @throws Exception
  */
  public function error($error, $errorSummary)
  {
      throw new Exception($error, $errorSummary);
  }
  /**
  * Application
  */
  protected function Application()
  {
  }
  /**
  *  execute app
  * @param array parameters
  * @return $this->caller
  */
  public function execute(array $parameters)
  {
      $this->Application();
      foreach (array('group', 'method', 'host', 'path') as $Getter) {
          $this->Getter($Getter);
      }
      $application = array('call' => $this->Getter('callback'),'parameters' => $parameters);
      $callback = $application['call'];
      $this->Setter('input', $application['parameters'], 'parameters');
      return $this->$callback();
  }
}
