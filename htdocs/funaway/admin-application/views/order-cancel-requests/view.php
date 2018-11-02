<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<div class="fixed_container">
    <div class="row">



        <div class="col-sm-12">  

            <h1>Order Detail</h1> 
            <div class="containerwhite">

                <aside class="grid_2">
                    <ul class="centered_nav js-centered-nav">
                        <li><a href="javascript:;" onclick="tab(1)">Order Details</a></li>
                        <li><a href="javascript:;" onclick="tab(2)" >Comments</a></li>
                        <li><a href="javascript:;" onclick="tab(3)">Edit Status</a></li>
                        <li><a href="javascript:;" onclick="tab(4)">Bank Account Details</a></li>
                    </ul>



                    <div id="listing">
                    </div>




                </aside>  
            </div>
        </div> 


    </div>
</div>

<script>
    var cancel_id = '<?php echo $cancel_id ?>';
</script>