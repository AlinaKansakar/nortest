<?php
/**
 * Client menu template
 *
 * @package qobrix
 */

 if ( ! is_user_logged_in() ) {
    $login_page = get_field( 'login_page', 'options' );
?>
<div id="Secondary-Nav" class="et_pb_with_border et_pb_module dsm_icon_list dsm_icon_list_0_tb_header">
    <div class="et_pb_module_inner">
        <ul class="dsm_icon_list_items dsm_icon_list_ltr_direction dsm_icon_list_layout_horizontal">
            <li class="dsm_icon_list_child dsm_icon_list_child_1_tb_header dsm_icon_list_child_tooltip">
                <a href="<?php echo esc_url( $login_page ); ?>" 
                class="dsm_icon_list_tooltip"
                data-tippy-arrow="true"
                data-tippy-placement="bottom"
                data-dsm-slug="dsm_icon_list_child_1_tb_header"
                data-tippy-content="Client Login"
                aria-expanded="false">
                    <span class="dsm_icon_list_wrapper">
                        <span class="dsm_icon_list_icon"></span>
                    </span>
                    <span class="dsm_icon_list_text">.</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<?php
 } else {
    $my_projects_page = get_field( 'my_projects_page', 'options' );
    $my_documents_page = get_field( 'my_documents_page', 'options' );
    $my_profile_page = get_field( 'my_profile_page', 'options' );
    ?>
    <div id="Secondary-Nav" class="et_pb_with_border et_pb_module dsm_icon_list dsm_icon_list_0_tb_header">
        <div class="et_pb_module_inner">
            <ul class="dsm_icon_list_items dsm_icon_list_ltr_direction dsm_icon_list_layout_horizontal">
                <li class="dsm_icon_list_child dsm_icon_list_child_1_tb_header dsm_icon_list_child_tooltip">
                    <div 
                    class="dsm_icon_list_tooltip dropdownDiv"
					data-tippy-arrow="true"
					data-tippy-placement="bottom"
					data-dsm-slug="dsm_icon_list_child_1_tb_header"
					data-tippy-content="Client Portal"	 
                    aria-expanded="false">
                        <span class="dsm_icon_list_wrapper">
                            <span class="dsm_icon_list_icon"></span>
                        </span>
                        <span class="dsm_icon_list_text">.</span>
						<div class="dropdown-content">
                            <a href="<?php echo esc_url( $my_profile_page ); ?>">My Profile</a>
                            <a href="<?php echo esc_url( $my_documents_page ); ?>">My Documents</a>
                            <a href="<?php echo esc_url( $my_projects_page ); ?>">My Projects</a>
                            <a href="<?php echo wp_logout_url(home_url()); ?>">Logout</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <?php
}