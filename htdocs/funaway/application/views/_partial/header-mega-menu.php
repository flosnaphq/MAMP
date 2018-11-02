<?php // done integration ?>
<nav class="menu main-menu small--menu js-main-menu">
    <ul class="list list--horizontal">
         
        <li class="sub-menu sub-menu--mega">
            <a href="javascript:void(0)"><?php echo Info::t_lang('Destination') ?></a>
             <ul class="list list--vertical sub-menu-dropdown">
                <li>
                    <div class="container container--static no--padding">
                        <div class="sub-menu--mega-content">
                            <ul>
                                <?php foreach ($headerRegions as $regions): ?>

                                    <li><a href="javascript:void(0)"><?php echo $regions['name']; ?></a>
                                        <div class="sub-menu--mega-side">
                                            <?php
                                            foreach ($regions['countries'] as $countryIdKey => $country):
                                                $countryUrl = Route::getRoute('country', 'details', array($countryIdKey));
                                                ?>
                                                <ul>
                                                    <li><a href="<?php echo $countryUrl; ?>"><?php echo $country['name']; ?></a></li>
                                                    <?php if ($country['cities']): ?>
                                                        <li>
                                                            <ul class="list list--horizontal">
                                                                <?php
                                                                foreach ($country['cities'] as $cityId => $city):
                                                                    $cityUrl = Route::getRoute('city', 'details', array($cityId));
                                                                    ?>
                                                                    <li><a href="<?php echo $cityUrl; ?>"><?php echo $city; ?></a></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>                                      

                                            <?php endforeach; ?>
                                        </div>
                                    </li>

                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </li>
        <li class="sub-menu sub-menu--mega">
            <a href="#"><?php echo Info::t_lang('ACTIVITY_TYPE') ?></a>
            <ul class="list list--vertical sub-menu-dropdown">
                <li>
                    <div class="container container--static no--padding">
                        <div class="sub-menu--mega-content">
                            <?php
                            $serviceCounter = 0;
                            $listLimit = 5;
                            $servicesParts = array_chunk($headerServices, $listLimit, true);

                            foreach ($servicesParts as $hserviceValue):
                                ?>

                                <ul class="list list--vertical">
                                    <?php
                                    foreach ($hserviceValue as $serviceId => $serviceValue):
                                        $serviceUrl = Route::getRoute('services', 'index', array($serviceId));
                                        ?>
                                        <li><a href="<?php echo $serviceUrl; ?>"><?php echo $serviceValue; ?></a></li>

                                    <?php endforeach; ?>
                                </ul>                                 
                            <?php endforeach; ?>
                        </div>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</nav>
