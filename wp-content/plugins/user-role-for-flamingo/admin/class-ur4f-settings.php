<?php

 /**
 * The class responsible for the  settings admin page.
 *
 * @package    user-role-for-flamingo
 * @subpackage user-role-for-flamingo/admin
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class UR4F_Admin_Settings {

    public function add_menu_page() {
        add_users_page(
            __('User Role For Flamingo - Capabilities', 'user-role-for-flamingo'),
			__('Flamingo User Role', 'user-role-for-flamingo'),
            'edit_users',
            'ur4f',
            array( $this , 'settings_page_html' )
        );
    }

    /**
     * Top level menu callback function
     */
    public function settings_page_html() {
        // check user capabilities
        if ( ! current_user_can( 'edit_users' ) ) {
            return;
        }
    
        // add error/update messages
    
        // check if the user have submitted the settings
        // WordPress will add the "settings-updated" $_GET parameter to the url
        if ( isset( $_GET['settings-updated'] ) ) {
            // add settings saved message with the class of "updated"
            add_settings_error( 'ur4f_messages', 'ur4f_message', __( 'Settings Saved', 'user-role-for-flamingo' ), 'updated' );
        }
    
        // show error/update messages
        settings_errors( 'ur4f_messages' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <p class="description">Tick the boxes for the available capabilities in the "Flamingo User" user role.</p>
            <form action="options.php" method="post">
                <?php
                    settings_fields( 'ur4f' );
                    do_settings_sections( 'ur4f' );
                    submit_button( __( 'Save Settings', 'user-role-for-flamingo' ) );
                ?>
            </form>
        </div>
        <?php
    }

    public function register_settings() {
        // Register a new setting for "ur4f" page.
        register_setting( 'ur4f', 'ur4f_options' );
        
        // Register a new section in the "ur4f" page.
        add_settings_section(
            'ur4f_section_contacts',
            __( 'Contact Capabilities', 'user-role-for-flamingo' ),
            function($args) {
                echo '';
            },
            'ur4f'
        );
        add_settings_section(
            'ur4f_section_inbound_messages',
            __( 'Inbound Message Capabilities', 'user-role-for-flamingo' ),
            function($args) {
                echo '';
            },
            'ur4f'
        );
        add_settings_section(
            'ur4f_section_outbound_messages',
            __( 'Outbound Message Capabilities', 'user-role-for-flamingo' ),
            function($args) {
                echo '';
            },
            'ur4f'
        );

        // Register all fields
        // Contacts
        add_settings_field(
            'ur4f_flamingo_edit_contacts',
            __( 'Show Contact List', 'user-role-for-flamingo' ),
            array( $this , 'render_checkbox'),
            'ur4f',
            'ur4f_section_contacts',
            array(
                'label_for' => 'ur4f_flamingo_edit_contacts',
                'class'     => 'ur4f_general_setting',
                'description' => __('Shows the admin menu item for contacts and allows access to the corresponding contacts page to show all contacts.', 'user-role-for-flamingo' )
            )
        );
        add_settings_field(
            'ur4f_flamingo_edit_contact',
            __( 'Edit Contact', 'user-role-for-flamingo' ),
            array( $this , 'render_checkbox'),
            'ur4f',
            'ur4f_section_contacts',
            array(
                'label_for' => 'ur4f_flamingo_edit_contact',
                'class'     => 'ur4f_general_setting',
                'description' => __('Allows to access the edit contact screen and actually editing the contact. Contacts can still be deleted depending on the "Delete Contact" capability. Only applies when access to "Show Contact List" is granted.', 'user-role-for-flamingo' )
            )
        );
        add_settings_field(
            'ur4f_flamingo_delete_contact',
            __( 'Delete Contact', 'user-role-for-flamingo' ),
            array( $this , 'render_checkbox'),
            'ur4f',
            'ur4f_section_contacts',
            array(
                'label_for' => 'ur4f_flamingo_delete_contact',
                'class'     => 'ur4f_general_setting',
                'description' => __('Allows to delete a single contact or multiple contacts at once. Only applies when access to "Show Contact List" is granted. In addition, this capability allows users with corresponding rights to delete contacts via the WP privacy personal data erasers.', 'user-role-for-flamingo' )
            )
        );

        // Inbound Messages
        add_settings_field(
            'ur4f_flamingo_edit_inbound_messages',
            __( 'Show Inbound Messages', 'user-role-for-flamingo' ),
            array( $this , 'render_checkbox'),
            'ur4f',
            'ur4f_section_inbound_messages',
            array(
                'label_for' => 'ur4f_flamingo_edit_inbound_messages',
                'class'     => 'ur4f_general_setting',
                'description' => __('Shows the admin menu item for inbound messages and allows access to the corresponding messages page to show all inbound messages.', 'user-role-for-flamingo' )
            )
        );
        add_settings_field(
            'ur4f_flamingo_edit_inbound_message',
            __( 'Edit Inbound Message', 'user-role-for-flamingo' ),
            array( $this , 'render_checkbox'),
            'ur4f',
            'ur4f_section_inbound_messages',
            array(
                'label_for' => 'ur4f_flamingo_edit_inbound_message',
                'class'     => 'ur4f_general_setting',
                'description' => __('Allows access to the edit screen for a single inbound message. There is nothing to edit besides the Spam/No Spam attribute, which works also, if "Spam Inbound Message" or "Unspam Inbound Message" are not granted. Only applies when access to "Show Inbound Messages" is granted.', 'user-role-for-flamingo' )
            )
        );
        add_settings_field(
            'ur4f_flamingo_delete_inbound_message',
            __( 'Delete Inbound Message', 'user-role-for-flamingo' ),
            array( $this , 'render_checkbox'),
            'ur4f',
            'ur4f_section_inbound_messages',
            array(
                'label_for' => 'ur4f_flamingo_delete_inbound_message',
                'class'     => 'ur4f_general_setting',
                'description' => __('Allows to trash a single inbound message or multiple inbound messages at once from the list screen and the edit screen. When there are items in the trash, the trash page can be accessed even without this capability. Only applies when access to "Show Inbound Messages" is granted. In addition, this capability allows users with corresponding rights to delete inbound messages via the WP privacy personal data erasers.', 'user-role-for-flamingo' )
            )
        );
        add_settings_field(
            'ur4f_flamingo_delete_inbound_messages',
            __( 'Show "Empty Trash" Button for Inbound Messages', 'user-role-for-flamingo' ),
            array( $this , 'render_checkbox'),
            'ur4f',
            'ur4f_section_inbound_messages',
            array(
                'label_for' => 'ur4f_flamingo_delete_inbound_messages',
                'class'     => 'ur4f_general_setting',
                'description' => __('Shows the "Empty Trash" Button on the trash page. It depends on the "Delete Inbound Message" capability, if inbound messages can actually be permanently deleted. Only applies when access to "Show Inbound Messages" is granted.', 'user-role-for-flamingo' )
            )
        );
        add_settings_field(
            'ur4f_flamingo_spam_inbound_message',
            __( 'Spam Inbound Message', 'user-role-for-flamingo' ),
            array( $this , 'render_checkbox'),
            'ur4f',
            'ur4f_section_inbound_messages',
            array(
                'label_for' => 'ur4f_flamingo_spam_inbound_message',
                'class'     => 'ur4f_general_setting',
                'description' => __('Allows user to spam a single inbound message or multiple inbound messages at once from the overview screens. Caution: Permission for spamming and unspamming a single inbound message from the edit inbound message screen is always granted.', 'user-role-for-flamingo' )
            )
        );
        add_settings_field(
            'ur4f_flamingo_unspam_inbound_message',
            __( 'Unspam Inbound Message', 'user-role-for-flamingo' ),
            array( $this , 'render_checkbox'),
            'ur4f',
            'ur4f_section_inbound_messages',
            array(
                'label_for' => 'ur4f_flamingo_unspam_inbound_message',
                'class'     => 'ur4f_general_setting',
                'description' => __('Allows user to unspam a single inbound message or multiple inbound messages at once from the overview screens. Caution: Permission for spamming and unspamming a single inbound message from the edit inbound message screen is always granted.', 'user-role-for-flamingo' )
            )
        );

        // Outbound Messages

        // Capability currently not used:
        // add_settings_field(
        //     'ur4f_flamingo_edit_outbound_messages',
        //     __( 'Show Outbound Messages', 'user-role-for-flamingo' ),
        //     array( $this , 'render_checkbox'),
        //     'ur4f',
        //     'ur4f_section_outbound_messages',
        //     array(
        //         'label_for' => 'ur4f_flamingo_edit_outbound_messages',
        //         'class'     => 'ur4f_general_setting',
        //         'description' => __('', 'user-role-for-flamingo' )
        //     )
        // );
        add_settings_field(
            'ur4f_flamingo_edit_outbound_message',
            __( 'Edit Outbound Message', 'user-role-for-flamingo' ),
            array( $this , 'render_checkbox'),
            'ur4f',
            'ur4f_section_outbound_messages',
            array(
                'label_for' => 'ur4f_flamingo_edit_outbound_message',
                'class'     => 'ur4f_general_setting',
                'description' => __('Allows user to edit outbound messages.', 'user-role-for-flamingo' )
            )
        );
        add_settings_field(
            'ur4f_flamingo_delete_outbound_message',
            __( 'Delete Outbound Message', 'user-role-for-flamingo' ),
            array( $this , 'render_checkbox'),
            'ur4f',
            'ur4f_section_outbound_messages',
            array(
                'label_for' => 'ur4f_flamingo_delete_outbound_message',
                'class'     => 'ur4f_general_setting',
                'description' => __('Allows user to delete a single outbound message or multiple outbound messages. Shows the "Empty Trash" button on the trash screen. When there are items in the trash, the trash page can be accessed even without this capability.', 'user-role-for-flamingo' )
            )
        );
    }

    public function render_checkbox($args) {
        $options = get_option('ur4f_options');
        ?>
        <input  type="checkbox" 
                id="<?php echo esc_attr($args['label_for']); ?>" 
                name="ur4f_options[<?php echo esc_attr($args['label_for']); ?>]"
                value="1"
                <?php isset($options[$args['label_for']]) ? (checked(1,$options[$args['label_for']])) : ('') ?>
        >
        <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php
    }
}
