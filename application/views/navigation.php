<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <?php
        $nav_photo = 'avatar.png';
        if ($this->session->userdata('photo')!=''){
            $nav_photo = $this->session->userdata('photo');
        }
        ?>
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?php echo base_url(); ?>assets/img_profile/<?php echo $nav_photo?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>
                    <?php 
                    $user_name= $this->session->userdata('fullname');
                    $str_name = strlen($user_name) > 16 ? substr($user_name, 0, 16) . '...': $user_name;
                    echo $str_name; 
                    ?>
                </p>
                <a href="#">Online</a>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            
            <?php
                
                $arr_data = $this->asik_model->get_privilege($this->session->userdata('priv_group_id'), $this->asik_model->action_view_data);
                if (isset($arr_data)){
                    $idx = 0;
                    $active_class = '';
                    $category = $arr_data[0];
                    $module = $arr_data[1];
                    foreach ($this->asik_model->category as $key => $value) {
                            $active_class = $active_li == $idx ? 'active':'';
                            for($c=0; $c<sizeof($category); $c++){
                                if ($key == $category[$c]){
                                    if (sizeof($this->asik_model->module[$idx])==1){
                                        $link = '';
                                        foreach ($this->asik_model->module[$idx] as $kunci => $nilai) {
                                            $link = $nilai[0];
                                        }
                                        echo '<li class="'.$active_class.'">
                                        <a href="'.site_url($link.'/go/'.$category[$c].$module[0]).'">
                                          '.$this->asik_model->icon[$idx].' <span>'.$value.'</span>
                                        </a>
                                        </li>';
                                    }
                                    if (sizeof($this->asik_model->module[$idx]) > 1){
                                        echo '<li class="'.$active_class.' treeview">';
                                        echo '<a href="#">'.$this->asik_model->icon[$idx].' ';
                                        echo '<span>'.$value.'</span>';
                                        echo '<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>';
                                        echo '</a>';
                                        echo '<ul class="treeview-menu">';
                                        foreach ($this->asik_model->module[$idx] as $kunci => $nilai) {
                                            for($m=0; $m<sizeof($module); $m++){
                                                if ($kunci == $module[$m]){
                                                    echo '<li><a href="'.site_url($nilai[0].'/go/'.$key.$kunci).'">';
                                                    echo '<i class="fa fa-circle-o"></i> '.$nilai[1].'</a></li>';
                                                    break;
                                                }
                                            }
                                        }
                                        echo '</ul>';
                                        echo '</li>';
                                        break;
                                    }
                                }
                            }
   
                        $idx++;
                    }
                }
            ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>