<?php
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

class Google_Service_Sheets_BubbleChartSpec extends Google_Model
{
  protected $bubbleBorderColorType = 'Google_Service_Sheets_Color';
  protected $bubbleBorderColorDataType = '';
  protected $bubbleLabelsType = 'Google_Service_Sheets_ChartData';
  protected $bubbleLabelsDataType = '';
  public $bubbleMaxRadiusSize;
  public $bubbleMinRadiusSize;
  public $bubbleOpacity;
  protected $bubbleSizesType = 'Google_Service_Sheets_ChartData';
  protected $bubbleSizesDataType = '';
  protected $bubbleTextStyleType = 'Google_Service_Sheets_TextFormat';
  protected $bubbleTextStyleDataType = '';
  protected $domainType = 'Google_Service_Sheets_ChartData';
  protected $domainDataType = '';
  protected $groupIdsType = 'Google_Service_Sheets_ChartData';
  protected $groupIdsDataType = '';
  public $legendPosition;
  protected $seriesType = 'Google_Service_Sheets_ChartData';
  protected $seriesDataType = '';

  public function setBubbleBorderColor(Google_Service_Sheets_Color $bubbleBorderColor)
  {
    $this->bubbleBorderColor = $bubbleBorderColor;
  }
  public function getBubbleBorderColor()
  {
    return $this->bubbleBorderColor;
  }
  public function setBubbleLabels(Google_Service_Sheets_ChartData $bubbleLabels)
  {
    $this->bubbleLabels = $bubbleLabels;
  }
  public function getBubbleLabels()
  {
    return $this->bubbleLabels;
  }
  public function setBubbleMaxRadiusSize($bubbleMaxRadiusSize)
  {
    $this->bubbleMaxRadiusSize = $bubbleMaxRadiusSize;
  }
  public function getBubbleMaxRadiusSize()
  {
    return $this->bubbleMaxRadiusSize;
  }
  public function setBubbleMinRadiusSize($bubbleMinRadiusSize)
  {
    $this->bubbleMinRadiusSize = $bubbleMinRadiusSize;
  }
  public function getBubbleMinRadiusSize()
  {
    return $this->bubbleMinRadiusSize;
  }
  public function setBubbleOpacity($bubbleOpacity)
  {
    $this->bubbleOpacity = $bubbleOpacity;
  }
  public function getBubbleOpacity()
  {
    return $this->bubbleOpacity;
  }
  public function setBubbleSizes(Google_Service_Sheets_ChartData $bubbleSizes)
  {
    $this->bubbleSizes = $bubbleSizes;
  }
  public function getBubbleSizes()
  {
    return $this->bubbleSizes;
  }
  public function setBubbleTextStyle(Google_Service_Sheets_TextFormat $bubbleTextStyle)
  {
    $this->bubbleTextStyle = $bubbleTextStyle;
  }
  public function getBubbleTextStyle()
  {
    return $this->bubbleTextStyle;
  }
  public function setDomain(Google_Service_Sheets_ChartData $domain)
  {
    $this->domain = $domain;
  }
  public function getDomain()
  {
    return $this->domain;
  }
  public function setGroupIds(Google_Service_Sheets_ChartData $groupIds)
  {
    $this->groupIds = $groupIds;
  }
  public function getGroupIds()
  {
    return $this->groupIds;
  }
  public function setLegendPosition($legendPosition)
  {
    $this->legendPosition = $legendPosition;
  }
  public function getLegendPosition()
  {
    return $this->legendPosition;
  }
  public function setSeries(Google_Service_Sheets_ChartData $series)
  {
    $this->series = $series;
  }
  public function getSeries()
  {
    return $this->series;
  }
}
