<?php
                for ($DAY=1; $DAY <= $CYCLE_DAYS; $DAY++){
                        print "#day$DAY";
                        if ($DAY != $CYCLE_DAYS)
                                print ",";
                }
                print "\n{ list-style: none; }\n";

                for ($DAY=1; $DAY <= $CYCLE_DAYS; $DAY++){
                        print "#day$DAY li";
                        if ($DAY != $CYCLE_DAYS)
                                print ",";
                }
                print "\n { border: solid 1px grey; margin: 2px;}\n";

                for ($DAY=1; $DAY <= $CYCLE_DAYS; $DAY++){
                        print "#day$DAY .ui-selecting";
                        if ($DAY != $CYCLE_DAYS)
                                print ",";
                }
                print "\n{ background: #FECA40; }\n";

                for ($DAY=1; $DAY <= $CYCLE_DAYS; $DAY++){
                        print "#day$DAY .ui-selected";
                        if ($DAY != $CYCLE_DAYS)
                                print ",";
                }
                print "\n{ background: #F39814; color: white; }\n";


?>

