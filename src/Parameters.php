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

use iSoftware\Engine\Exception as Exception;

/**
  * Parameters
  *
  * @author  iSoftware <iSoftware.NGO@gmail.com>
  * @license http://www.gnu.org/licenses/agpl-3.0
  * @link    https://github.com/i-Software/Enigne
  */

class Parameters
{

/**
  * @var array
  */
  protected $parameters;

 /**
 * @var array
 */
 protected $Enumeration = array(
   "type" => array("client","request"),
   "parameter" => "/^[A-z0-9\-\_]{1,64}/",
   "pattern" => "%preg_match()%",
   "parameters" => array(
      "require",
      "parameter",
      "name",
      "pattern",
      "input",
      "type"
    )
 );

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
 * @return array
 */
  public function getParameters()
  {
      return $this->parameters;
  }

  /**
  * @param int $x
  * @param string $Getter
  * @throws Exception
  * @return Boolean
  */
  protected function Getter($Getter,  $x)
  {
      $Getter = trim(strtolower($Getter));
      $Parameters = $this->getParameters();
      if (!isset($Parameters[$x])) {
          $this->error("ParseException", "'parameter no.$x' is not found");
      }
      if (!is_array($Parameters[$x])) {
          $this->error("ParseException", "'parameter no.$x' is not a valid parameter");
      }
      $Parameter = $Parameters[$x];
      if (!isset($Parameter['require'])) {
          $this->error("ParseException", "'parameter no.$x' is missing 'require' option");
      } elseif (!isset($Parameter['parameter'])) {
          $this->error("ParseException", "'parameter no.$x' is missing 'parameter' option");
      } elseif (!isset($Parameter['pattern'])) {
          $this->error("ParseException", "'parameter no.$x' is missing 'pattern' option");
      } elseif (!isset($Parameter['input'])) {
          $this->error("ParseException", "'parameter no.$x' is missing 'input' option");
      } elseif (!isset($Parameter['type'])) {
          $this->error("ParseException", "'parameter no.$x' is missing 'type' option");
      } elseif (!isset($Parameter['name'])) {
          $this->error("ParseException", "'parameter no.$x' is missing 'name' option");
      }
      switch ($Getter) {
        case 'require':
          if (!is_bool($Parameter['require'])) {
              $this->error("ParseException", "'require' option in 'parameter no.$x' must be a boolean format (true|false|1|0)");
          }
          $value = $Parameter['require'];
        break;
        case 'name':
          if (!preg_match($this->Enumeration['parameter'], $Parameter['name'])) {
              $this->error("ParseException", "'name' option in 'parameter no.$x' does not match pattern [[A-z0-9\-\_]{3,64}]");
          }
          $value = $Parameter['name'];
        break;
        case 'parameter':
          if (!preg_match($this->Enumeration['parameter'], $Parameter['parameter'])) {
              $this->error("ParseException", "'parameter->b' option in 'parameter no.$x' does not match pattern [[A-z0-9\-\_]{3,64}]");
          }
          $value = $Parameter['parameter'];
        break;
        case 'pattern':
          @preg_match($Parameter['pattern'], md5(rand(0, 100)));
          if (error_get_last() && preg_match($this->Enumeration['pattern'], error_get_last()['message'])) {
              $this->error("ParseException", "'pattern' option in 'parameter no.$x' is not a valid pattern (".error_get_last()['message'].")");
          }
          $value = $Parameter['pattern'];
        break;
        case 'type':
          if (!in_array($Parameter['type'], $this->Enumeration['type'])) {
              $this->error("ParseException", "'type' option in 'parameter no.$x'".' is not in enumeration [' . implode(', ', $this->Enumeration["type"]). ']');
          }
          $value = $Parameter['type'];
        break;
        case 'input':
            $value = $Parameter['input'];
        break;
      }
      return (isset($value)) ? $value:null;
  }

  /**
  * @param string $pattern,
  * @param string $data
  * @return Boolean
  */
  protected function Pattern($pattern,  $data)
  {
      if (preg_match($pattern, $data) == true) {
          return true;
      } else {
          return false;
      }
  }

  /**
  * @param string $input
  * @param int $x
  * @return Boolean
  */
  protected function insertInput($input, $x)
  {
      $getInput = trim($input);
      if ($this->Getter('type', $x) == 'client') {
          $this->parameters[$x]['input'] = $input;
      }
      return ($this->Getter('input', $x) == $input)? true:false;
  }

  /**
  * @param array $Parameters
  * @return boolean
  */
  protected function Parameters(array $Parameters)
  {
      $getParameters = array("a" => $this->getParameters(),"b" => $Parameters);
      for ($x=0; $x < count($getParameters['a']); $x++) {
          foreach ($this->Enumeration['parameters'] as $param) {
              if (gettype($this->Getter($param, $x)) != 'NULL') {
                  $Parameter[$param] =  $this->Getter($param, $x);
              }
          }
          if (isset($Parameter['require']) && isset($Parameter['parameter']) &&
              isset($Parameter['name']) && isset($Parameter['pattern']) &&
              isset($Parameter['type']) && isset($Parameter['input'])) {
              if (isset($getParameters['b'][$Parameter['name']])) {
                  if ($Parameter['type'] == 'client') {
                      $input = $getParameters['b'][$Parameter['name']];
                      $this->insertInput($input, $x);
                  }
              } else {
                  if ($Parameter['type'] == 'client') {
                      $this->error("ParametersException", "Missing argument <".$Parameter['name'].">");
                  }
              }
              $newInput = $this->Getter('input', $x);
              if ($Parameter['require'] == true) {
                  $Pattern = $this->Pattern($Parameter['pattern'], $newInput);
                  if (!$Pattern) {
                      $this->error("ParametersException", "<".$Parameter['name']."> does not match pattern [".$Parameter['pattern']."]");
                  }
                  if (empty($newInput)) {
                      $this->error("ParametersException", "<".$Parameter['name']."> is required");
                  }
              }
          }
      }
  }

   /**
   * @return array
   */
   public function toArray()
   {
       return (array) $this->toArray;
   }

  /**
  * @param array $appParameters;
  * @param array $clientParameters;
  */
  public function __construct(array $appParameters, array $clientParameters)
  {
      $this->parameters = $appParameters;
      $toArray = array();
      $this->Parameters($clientParameters);
      for ($x=0; $x < count($this->getParameters()); $x++) {
          if ($this->Getter('input', $x) && $this->Getter('parameter', $x)) {
              $toArray[$this->Getter('parameter', $x)] = $this->Getter('input', $x);
          }
      }

      $this->toArray = $toArray;
  }
}
