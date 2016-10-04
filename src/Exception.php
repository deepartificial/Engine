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

/**
  * Exception
  *
  * @author  iSoftware <iSoftware.NGO@gmail.com>
  * @license http://www.gnu.org/licenses/agpl-3.0
  * @link    https://github.com/i-Software/Enigne
  */

  class Exception extends \Exception
  {
      public function __construct($error, $errorSummary)
      {
          $this->Exception = array('error' => $error,'errorSummary'=> $errorSummary);
          $this->message = json_encode($this->Exception, 128|64);
      }
  }
