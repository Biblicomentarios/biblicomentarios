<?php

namespace Google\Site_Kit_Dependencies;

/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
class Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1alphaConversionEvent extends \Google\Site_Kit_Dependencies\Google_Model
{
    public $createTime;
    public $eventName;
    public $isDeletable;
    public $name;
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
    }
    public function getCreateTime()
    {
        return $this->createTime;
    }
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;
    }
    public function getEventName()
    {
        return $this->eventName;
    }
    public function setIsDeletable($isDeletable)
    {
        $this->isDeletable = $isDeletable;
    }
    public function getIsDeletable()
    {
        return $this->isDeletable;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
}
