<?php

function volunteer_theme_support() {

    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    // Custom background color.
    add_theme_support(
        'custom-background',
        array(
            'default-color' => 'f5efe0',
        )
    );

    // Set content-width.
    global $content_width;
    if ( ! isset( $content_width ) ) {
        $content_width = 580;
    }

    /*
     * Enable support for Post Thumbnails on posts and pages.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 1200, 9999 );
    add_image_size( 'news', 512, 325, true );
    add_image_size( 'full-hd', 1920, 0, false );
    add_image_size( 'full-xl', 1366, 0, false );
    add_image_size( 'full-lg', 1200, 0, false );
    add_image_size( 'full-md', 992, 0, false );
    add_image_size( 'full-sm', 768, 0, false );
    add_image_size( 'full-xs', 576, 0, false );

    // Custom logo.
    $logo_width  = 147;
    $logo_height = 36;

    // If the retina setting is active, double the recommended width and height.
    if ( get_theme_mod( 'retina_logo', false ) ) {
        $logo_width  = floor( $logo_width * 2 );
        $logo_height = floor( $logo_height * 2 );
    }

    add_theme_support(
        'custom-logo',
        array(
            'height'      => $logo_height,
            'width'       => $logo_width,
            'flex-height' => true,
            'flex-width'  => true,
            'header-text' => array( 'site-title', 'site-description' ),
        )
    );

    /*
     * Let WordPress manage the document title.
     * By adding theme support, we declare that this theme does not use a
     * hard-coded <title> tag in the document head, and expect WordPress to
     * provide it for us.
     */
    add_theme_support( 'title-tag' );
    add_theme_support( 'favicon' );

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'script',
            'style',
        )
    );

    /*
     * Make theme available for translation.
     * Translations can be filed in the /languages/ directory.
     * If you're building a theme based on Twenty Twenty, use a find and replace
     * to change 'twentytwenty' to the name of your theme in all the template files.
     */
    load_theme_textdomain( 'jobsearch' );

    // Add support for full and wide align images.
    add_theme_support( 'align-wide' );

}

add_action( 'after_setup_theme', 'volunteer_theme_support' );


/**
 * Register navigation menus uses wp_nav_menu in five places.
 */
function volunteer_menus() {

    $locations = array(
        'main_menu' => __( 'Main menu', 'jobsearch' ),
        'site_menu' => __( 'Site menu', 'jobsearch' ),
    );

    register_nav_menus( $locations );
}

add_action( 'init', 'volunteer_menus' );

/**
 * Get the information about the logo.
 *
 * @param string $html The HTML output from get_custom_logo (core function).
 *
 * @return string $html
 */
function volunteer_get_custom_logo( $html ) {

    $logo_id = get_theme_mod( 'custom_logo' );

    if ( ! $logo_id ) {
        return $html;
    }

    $logo = wp_get_attachment_image_src( $logo_id, 'full' );

    if ( $logo ) {
        // For clarity.
        $logo_width  = esc_attr( $logo[1] );
        $logo_height = esc_attr( $logo[2] );

        // If the retina logo setting is active, reduce the width/height by half.
        if ( get_theme_mod( 'retina_logo', false ) ) {
            $logo_width  = floor( $logo_width / 2 );
            $logo_height = floor( $logo_height / 2 );

            $search = array(
                '/width=\"\d+\"/iU',
                '/height=\"\d+\"/iU',
            );

            $replace = array(
                "width=\"{$logo_width}\"",
                "height=\"{$logo_height}\"",
            );

            // Add a style attribute with the height, or append the height to the style attribute if the style attribute already exists.
            if ( strpos( $html, ' style=' ) === false ) {
                $search[]  = '/(src=)/';
                $replace[] = "style=\"height: {$logo_height}px;\" src=";
            } else {
                $search[]  = '/(style="[^"]*)/';
                $replace[] = "$1 height: {$logo_height}px;";
            }

            $html = preg_replace( $search, $replace, $html );

        }
    }

    return $html;

}

add_filter( 'get_custom_logo', 'volunteer_get_custom_logo' );

if ( ! function_exists( 'wp_body_open' ) ) {

    /**
     * Shim for wp_body_open, ensuring backwards compatibility with versions of WordPress older than 5.2.
     */
    function wp_body_open() {
        do_action( 'wp_body_open' );
    }
}

remove_action( 'wp_head', 'wp_generator' );

function restrict_rest_api_to_localhost() {
    /*$whitelist = [ '127.0.0.1', "::1" ];
    if ( ! in_array( $_SERVER['REMOTE_ADDR'], $whitelist ) ) {
        die( 'REST API is disabled.' );
    }*/
    if(!is_user_logged_in()){
        die( 'REST API is disabled.' );
    }
}

add_action( 'rest_api_init', 'restrict_rest_api_to_localhost', 0 );

function volunteer_login_stylesheet() {
    wp_enqueue_style('custom-login', get_template_directory_uri() .'/assets/css/style-login.css',array(), 1.1 );
    wp_enqueue_script('login-js', get_template_directory_uri() . '/assets/js/login-scripts.js',array( 'jquery' ),1.2);
}
add_action( 'login_enqueue_scripts', 'volunteer_login_stylesheet' );

function volunteer_register_styles() {

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if(strpos( $user_agent, 'Lighthouse') !== false || strpos($user_agent, 'Speed Insights') !== false ) {

    }else{
        if(is_front_page()){
            wp_enqueue_style( 'bootstrap-style', get_template_directory_uri() . '/vendors/bootstrap/css/bootstrap-grid.min.css', array(), '1.1' );
        }else{
            wp_enqueue_style( 'bootstrap-style', get_template_directory_uri() . '/vendors/bootstrap/css/bootstrap.min.css', array(), '1.1' );
        }

        if(!is_front_page()){
            wp_enqueue_style( 'select2-style', get_template_directory_uri() . '/vendors/select2/select2.min.css', array(), '1.1' );
            wp_enqueue_style( 'learning-press-style', get_template_directory_uri().'/assets/css/learning-press.css', array(), '1.1' );
        }
        wp_enqueue_style( 'volunteer-style', get_template_directory_uri().'/style.css', array(), '1.34' );

        if (is_page_template('page-entrenadores.php') || is_page_template('page-estadisticas.php')) {
            wp_enqueue_style('daterangepicker-style', get_stylesheet_directory_uri() . '/vendors/daterangepicker/daterangepicker.css', array(), '1.1');
        }
     }
}

add_action( 'get_footer', 'volunteer_register_styles' );



function ausuf_register_scripts() {

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if(strpos( $user_agent, 'Lighthouse') !== false || strpos($user_agent, 'Speed Insights') !== false ) {
        wp_enqueue_style( 'volunteer-style', get_template_directory_uri().'/style.css', array(), '1.34', 'all' );
    }else{

    }

    if(!is_user_logged_in() && is_front_page()){
        wp_enqueue_script('jquery-cookie', get_template_directory_uri() . '/assets/js/jquery.cookie.js',array( 'jquery' ), '1.1');
    }

    if (is_page_template('page-interviews.php') || is_page_template('page-meeting.php')) {
        wp_enqueue_script('prettyembed-js', get_template_directory_uri() . '/vendors/prettyembed/jquery.prettyembed.min.js',array( 'jquery' ), '1.1');
    }

    if(is_user_logged_in()){
        wp_enqueue_script( 'select2-js', get_template_directory_uri() . '/vendors/select2/select2.min.js',array( 'jquery' ), '1.1' );
    }

    wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/vendors/bootstrap/js/bootstrap.min.js',array( 'jquery' ) , '1.1');
    wp_enqueue_script( 'volunteer-js', get_template_directory_uri() . '/assets/js/scripts.js',array( 'jquery' ), '1.17' );
    wp_script_add_data( 'volunteer-js', 'async', true );
    wp_localize_script( 'volunteer-js', 'admin_url', admin_url() );
    if(is_user_logged_in()==true){
        wp_localize_script( 'volunteer-js', 'user_login', 'true' );
    }else{
        wp_localize_script( 'volunteer-js', 'user_login', 'false' );
    }
    wp_localize_script( 'volunteer-js', 'language_domain', pll_current_language() );
    wp_localize_script( 'volunteer-js', 'home_url', esc_url( home_url( '/' ) ) );

    if(is_user_logged_in()) {
        wp_enqueue_script('jquery-yutube', 'https://www.youtube.com/player_api', false);
    }

    if (is_page_template('page-perfil.php')) {
        wp_enqueue_script('perfil-js', get_template_directory_uri() . '/assets/js/perfil.js',array( 'jquery' ), '1.1');
        wp_localize_script('perfil-js', 'admin_url', admin_url());
        wp_localize_script('perfil-js', 'security_perfil', wp_create_nonce('update_perfil'));
    }

    if (is_page_template('page-make-volunteer.php')) {
        wp_enqueue_style( 'smartwizard-style', get_template_directory_uri() . '/vendors/jquery-smartwizard-master/dist/css/smart_wizard.min.css', array(), '1.1' );
        wp_enqueue_style( 'smartwizard-all-style', get_template_directory_uri() . '/vendors/jquery-smartwizard-master/dist/css/smart_wizard_all.min.css', array(), '1.1');
        wp_enqueue_style( 'smartwizard-dots-style', get_template_directory_uri() . '/vendors/jquery-smartwizard-master/dist/css/smart_wizard_dots.min.css', array(), '1.1' );
        wp_enqueue_script( 'smartwizard-js', get_template_directory_uri() . '/vendors/jquery-smartwizard-master/dist/js/jquery.smartWizard.js',array( 'jquery' ), '1.1');
        wp_enqueue_script('make-volunteer-js', get_template_directory_uri() . '/assets/js/make-volunteer.js',array( 'jquery' ), '1.7');
        wp_localize_script('make-volunteer-js', 'admin_url', admin_url());
        wp_localize_script('make-volunteer-js', 'language_domain', pll_current_language() );
        wp_localize_script('make-volunteer-js', 'security_perfil', wp_create_nonce('update_perfil'));
    }

    if (is_page_template('page-training.php')) {
        wp_enqueue_script('volunteer-training-js', get_template_directory_uri() . '/assets/js/volunteer-training.js',array( 'jquery' ), '1.2');
        wp_localize_script('volunteer-training-js', 'admin_url', admin_url());
        wp_localize_script('volunteer-training-js', 'language_domain', pll_current_language() );
        wp_localize_script('volunteer-training-js', 'sold_out_text', pll__('Places are sold out') );
        wp_localize_script('volunteer-training-js', 'security_perfil', wp_create_nonce('update_perfil'));
    }

    if (is_page_template('page-entrenadores.php')) {
        wp_enqueue_script('moment-js', get_template_directory_uri() . '/vendors/daterangepicker/moment.min.js', array(), '1.1');
        wp_enqueue_script('daterangepicker-js', get_template_directory_uri() . '/vendors/daterangepicker/daterangepicker.min.js', array(), '1.1');
        wp_enqueue_script('volunteer-entrenadores-js', get_template_directory_uri() . '/assets/js/volunteer-entrenadores.js',array( 'jquery' ), '1.1');
        wp_localize_script('volunteer-entrenadores-js', 'admin_url', admin_url());
        wp_localize_script('volunteer-entrenadores-js', 'language_domain', pll_current_language() );
        wp_localize_script('volunteer-entrenadores-js', 'security_perfil', wp_create_nonce('update_perfil'));
    }

    if (is_page_template('page-estadisticas.php') || is_page_template('page-estadisticas-paises.php') || is_page_template('page-estadisticas-est.php') || is_page_template('page-estadisticas-vol.php')) {
        wp_enqueue_script('moment-js', get_template_directory_uri() . '/vendors/daterangepicker/moment.min.js', array(), '1.1');
        wp_enqueue_script('daterangepicker-js', get_template_directory_uri() . '/vendors/daterangepicker/daterangepicker.min.js', array(), '1.1');
        wp_enqueue_script('volunteer-entrenadores-js', get_template_directory_uri() . '/assets/js/volunteer-estadisticas.js',array( 'jquery' ), '1.12');
        wp_localize_script('volunteer-entrenadores-js', 'admin_url', admin_url());
        wp_localize_script('volunteer-entrenadores-js', 'security_perfil', wp_create_nonce('update_perfil'));
        wp_localize_script('volunteer-entrenadores-js', 'language_domain', pll_current_language() );
        wp_localize_script('volunteer-entrenadores-js', 'security_perfil', wp_create_nonce('update_perfil'));
    }

    if (is_page_template('page-mantenimiento.php')) {
        wp_enqueue_script('moment-js', get_template_directory_uri() . '/vendors/daterangepicker/moment.min.js', array(), '1.1');
        wp_enqueue_script('daterangepicker-js', get_template_directory_uri() . '/vendors/daterangepicker/daterangepicker.min.js', array(), '1.1');
        wp_enqueue_script('volunteer-entrenadores-js', get_template_directory_uri() . '/assets/js/volunteer-mantenimiento.js',array( 'jquery' ), '1.12');
        wp_localize_script('volunteer-entrenadores-js', 'admin_url', admin_url());
        wp_localize_script('volunteer-entrenadores-js', 'security_perfil', wp_create_nonce('update_perfil'));
        wp_localize_script('volunteer-entrenadores-js', 'language_domain', pll_current_language() );
        wp_localize_script('volunteer-entrenadores-js', 'security_perfil', wp_create_nonce('update_perfil'));
    }

    if (is_page_template('page-estadisticas-email.php')) {
        wp_enqueue_script('volunteer-entrenadores-js', get_template_directory_uri() . '/assets/js/volunteer-estadisticas-email.js',array( 'jquery' ), '1.4');
        wp_localize_script('volunteer-entrenadores-js', 'admin_url', admin_url());
        wp_localize_script('volunteer-entrenadores-js', 'security_perfil', wp_create_nonce('update_perfil'));
        wp_localize_script('volunteer-entrenadores-js', 'language_domain', pll_current_language() );
    }

    if (is_page_template('page-volunteers.php')) {
        wp_enqueue_script('search-volunteer-js', get_template_directory_uri() . '/assets/js/search-volunteers.js',array( 'jquery' ), '1.1');
        wp_localize_script('search-volunteer-js', 'admin_url', admin_url());
        wp_localize_script('search-volunteer-js', 'security_perfil', wp_create_nonce('update_perfil'));
    }
}
add_action( 'wp_enqueue_scripts', 'ausuf_register_scripts' );

// Redirect user to home on logout
add_action('wp_logout', 'volunteer_after_logout');
function volunteer_after_logout()
{
    wp_redirect(home_url());
    exit();
}

function login_redirect( $redirect_to, $request, $user ){
    return home_url();
}
add_filter( 'login_redirect', 'login_redirect', 10, 3 );


function disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );

function disable_emojis_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
}

function wpabsolute_block_users_backend() {
    if ( is_admin() && ! current_user_can( 'administrator' ) && ! wp_doing_ajax() ) {
        wp_redirect( home_url() );
        exit;
    }
}
add_action( 'init', 'wpabsolute_block_users_backend' );

function metas_seo() {
    global $wp;
    $output = '';
    $output .= '<meta name="google-site-verification" content="dllTD57Cc-c5rMTUFFbDaGki6Jp9qsbnjP3LFhWFrt0" />';
    echo $output;
}
add_action( 'wp_head', 'metas_seo' );

function get_volunteer_header_image(){
    if(empty(get_post_field('image_header',get_the_ID()))){
        $srcset = '<img src="'.get_template_directory_uri().'/assets/images/page_title.jpg'.'">';
        return $srcset;
    }else{

        $srcset = wp_get_attachment_image( get_post_field('image_header',get_the_ID()), array('1366', '992', '768'), "", array( "class" => "img-responsive" ) );
        return $srcset;
    }
}

function get_volunteer_header_text(){
    $output = '';
    if(!empty(get_post_field('description_header',get_the_ID()))){
        $output = '<section id="header-title-page">
                        <div class="header-title-page-inner">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="volunteer-header-text">';
        $output .= get_post_field('description_header',get_the_ID());
        $output .= '</div> </div>
            </div>
        </div>
    </div>
</section>';
    }
    return $output;
}

function volunteer_add_new_zone_widgets() {
    register_sidebar( array(
        'name'          => 'Language',
        'id'            => 'language',
        'description'   => 'Se muestra en el area de idioma',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'volunteer_add_new_zone_widgets' );

function my_class_names($classes) {
    $post =  get_post( get_the_ID() );
    if($post->post_type=='lp_course'){
        $classes[] = 'lp-landing';
    }
    if(is_user_logged_in()){
        if(get_user_meta(get_current_user_id(),'volunteer_user')[0]=='yes'){
            $classes[] = 'volunteer-access';
        }
        if (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())  || in_array('operador', get_current_user_roles())){
            $classes[] = 'volunteer-access';
        }
        if (in_array('administrator', get_current_user_roles()) || in_array('entrenador', get_current_user_roles())  || in_array('operador', get_current_user_roles())){
            $classes[] = 'coach-access';
        }
    }


    if( !is_front_page() ) $classes[] = 'not-home';
    return $classes;
}
add_filter('body_class','my_class_names');

function getUrl($url, $expires = 5)
{
    $options = array(
        'http'=>array(
            'method'=> "GET",
            'header'=>
                "Accept-language: en\r\nUser-Agent: Just A Simple Request-er :)\r\n" // i.e. An iPad

        )
    );
    $file = file_get_contents($url, false, stream_context_create($options));
    return $file;
}

function wp_get_duracion($duracion){
    if($duracion>=60){
        $hours = floor($duracion/60);
        $minutes = ($duracion%60);
        $hours = $hours.'h';
        if($minutes!=0): $minutes = ' '.$minutes.' min'; else: $minutes = ''; endif;
        $duracion =  $hours.$minutes;
    }elseif($duracion>=1){
        $minutes = ($duracion%60).' min';
        $duracion =  $minutes;
    }else{
        $duracion = '';
    }
    return $duracion;
}

add_action('init', 'blockusers_init');
function blockusers_init()
{
    if (is_user_logged_in()) {
        if(empty(get_user_meta(get_current_user_id(),'hash_user')[0])){
            update_user_meta(get_current_user_id(),'hash_user', md5(rand()));
        }

    }
}

function get_current_user_roles() {
    $current_user = get_current_user_id();
    $user_meta = get_userdata($current_user);
    $user_roles = $user_meta->roles;
    return $user_roles;
}

function get_user_roles_($id) {
    $user_meta = get_userdata($id);
    $user_roles = $user_meta->roles;
    return $user_roles;
}

add_action( 'wp_ajax_file_upload', 'file_upload_callback' );
add_action( 'wp_ajax_nopriv_file_upload', 'file_upload_callback' );
function file_upload_callback() {
    $response = [];

    $attachment_id = media_handle_upload( 'file', 0 );

    if ( is_wp_error( $attachment_id ) ) {

        $response['response'] = "ERROR";
        $response['error']    = 'Error al subir su archivo.';

    } else {
        $fullsize_path = get_attached_file( $attachment_id );

        $pathinfo = pathinfo( $fullsize_path );
        $url      = wp_get_attachment_url( $attachment_id );

        $response['response']      = "SUCCESS";
        $response['attachment_id'] = $attachment_id;
        $response['filename']      = $pathinfo['filename'];
        $response['url']           = $url;
        $type                      = $pathinfo['extension'];
        if ( $type == "jpeg"
            || $type == "jpg"
            || $type == "png"
            || $type == "gif"
        ) {
            $type = "image/" . $type;
        }
        $response['type'] = $type;
    }
    echo json_encode( $response );

    wp_die();
}

add_action( 'wp_ajax_make_volunteer', 'make_volunteer' );
add_action( 'wp_ajax_nopriv_make_volunteer', 'make_volunteer' );
function make_volunteer() {
    check_ajax_referer( 'update_perfil', 'security' );
    if ( isset( $_POST['data'] ) && is_user_logged_in() ) {
        parse_str( $_POST['data'], $form_data );

        $land    = true;
        $message = '';

        $user_id_id = sanitize_text_field($form_data['user_id_id']);
        if(empty($user_id_id)){
            $land = false;
            $message .=  pll__('ID required'). '. ';
        }else{
            if(filter_var( $user_id_id, FILTER_SANITIZE_NUMBER_INT ) == false){
                $land    = false;
                $message .=  pll__('Invalid ID'). '. ';
            }
        }

        /*if(get_user_meta(get_current_user_id(),'country_user_new')[0]=='PY'){
            $user_agreement_local_id = sanitize_text_field($form_data['user_agreement_local_id']);
            if(empty($user_agreement_local_id)){
                $land = false;
                $message .=  pll__('Agreement local required'). '. ';
            }else{
                if(filter_var( $user_agreement_local_id, FILTER_SANITIZE_NUMBER_INT ) == false){
                    $land    = false;
                    $message .=  pll__('Invalid Agreement local'). '. ';
                }
            }
        }*/

        $user_agreement_id = sanitize_text_field($form_data['user_agreement_id']);
        if(empty($user_agreement_id)){
            $land = false;
            $message .=  pll__('Agreement required'). '. ';
        }else{
            if(filter_var( $user_agreement_id, FILTER_SANITIZE_NUMBER_INT ) == false){
                $land    = false;
                $message .=  pll__('Invalid Agreement'). '. ';
            }
        }
        $user_cts_id = sanitize_text_field($form_data['user_cts_id']);
        if(empty($user_cts_id)){
            $land = false;
            $message .=  pll__('CTS Certificate required'). '. ';
        }else{
            if(filter_var( $user_cts_id, FILTER_SANITIZE_NUMBER_INT ) == false){
                $land    = false;
                $message .= pll__('Invalid CTS Certificate'). '. ';
            }
        }
        $names_user = sanitize_text_field($form_data['profile_firstname']);
        if(empty($names_user)){
            $land = false;
            $message .= pll__('First Name required'). '. ';
        }
        $surnames_users = sanitize_text_field($form_data['profile_lastname']);
        if(empty($surnames_users)){
            $land = false;
            $message .=  pll__('Last Name required'). '. ';
        }

        $terms_of_service = $form_data['terms_of_service'];
        if(empty($terms_of_service)){
            $land = false;
            $message .=  pll__('Accept Terms and Conditions'). '. ';
        }


        $phone_number_user = sanitize_text_field($form_data['profile_phone']);
        $profile_language = sanitize_text_field($form_data['profile_language']);
        $profile_languageother = sanitize_text_field($form_data['profile_languageother']);

        $profile_region = sanitize_text_field($form_data['profile_region']);
        $profile_state_region = sanitize_text_field($form_data['profile_state_region']);
        if(empty($profile_region) || empty($profile_state_region)){
            $land = false;
            $message .=  pll__('Select region'). '. ';
        }else{

        }

        if ( $land ) {
            if(empty(get_user_meta(get_current_user_id(),'hash_user')[0])){
                update_user_meta(get_current_user_id(),'hash_user', md5(rand()));
            }
            update_user_meta(get_current_user_id(),'firstname_user',$names_user);
            update_user_meta(get_current_user_id(),'lastname_user',$surnames_users);
            update_user_meta(get_current_user_id(),'language_user',$profile_language);
            update_user_meta(get_current_user_id(),'languageother_user',$profile_languageother);
            update_user_meta(get_current_user_id(),'id_user',$user_id_id);

            update_user_meta(get_current_user_id(),'agreement_user',$user_agreement_id);

            if(!empty($user_agreement_local_id)){
                update_user_meta(get_current_user_id(),'agreement_local_user',$user_agreement_local_id);
            }

            update_user_meta(get_current_user_id(),'cts_certificate_user',$user_cts_id);
            update_user_meta(get_current_user_id(),'phone_user',$phone_number_user);

            update_user_meta(get_current_user_id(),'volunteer_user', 'yes');
            update_user_meta(get_current_user_id(),'region_user',$profile_region);
            update_user_meta(get_current_user_id(),'region_state_user',$profile_state_region);

            $register = register_user(wp_get_current_user()->user_email,$names_user,$surnames_users,'1337960258' );

            // User Mail
            $subject_user = 'Greetings from the Contact Tracing Task-force Readiness Program (CRP)!';
            $to_user = wp_get_current_user()->user_email;
            $headers = array('Content-Type: text/html; charset=UTF-8');
            if(pll_current_language()=='en'):
                $message_mail_user = get_post_field('post_content',209);
            elseif(pll_current_language()=='hat'):
                $message_mail_user = get_post_field('post_content',2914);
            else:
                $message_mail_user = get_post_field('post_content',211);
            endif;
            $message_mail_user = str_replace('#name',get_user_meta(get_current_user_id(),'firstname_user')[0].' '.get_user_meta(get_current_user_id(),'lastname_user')[0],$message_mail_user);
            wp_mail( $to_user, $subject_user, $message_mail_user, $headers);

            // Heads Mail
            $heads = get_heads_by_region($profile_region);
            $subject = 'New Volunteer Information';

            $message_mail = '<img src="'.get_template_directory_uri() . '/assets/images/logo-black.jpg"><br/><br/>';
            $message_mail .= '<strong>Name: </strong>'.get_user_meta(get_current_user_id(),'firstname_user')[0].' '.get_user_meta(get_current_user_id(),'lastname_user')[0].'<br/><br/>';
            $message_mail .= '<strong>Language(s): </strong>'.get_user_meta(get_current_user_id(),'language_user')[0];
            if(!empty(get_user_meta(get_current_user_id(),'languageother_user')[0])){
                $message_mail .= ', '.get_user_meta(get_current_user_id(),'languageother_user')[0];
            }
            $message_mail .= '<br/><br/><strong>Email: </strong>'.wp_get_current_user()->user_email.'<br/><br/>';
            $message_mail .= '<strong>Region: </strong>'.$profile_region." (".$profile_state_region.")".'<br/><br/>';
            $message_mail .= '
                <p>Please see Attachments for more details!
                <p>Thanks! [CAUTION, this email is from outside of the organization Larkin Health System. Do not open any link or attachment if you are not sure if it is safe.]
                <p>CONFIDENTIALITY NOTICE: Information contained in this transmission, together with any other documents or attachments, is privileged and confidential, and is intended only for the use of the individual or entity to which it is addressed. This transmission may contain information or materials protected by the applicable laws of the State of Florida and /or protected health information as defined by the Health Insurance Portability and Accountability Act of 1996 (HIPAA). This information is intended exclusively for the use of the individual or entity named as addressee(s). The authorized recipient of this information is STRICTLY PROHIBITED from disclosing this information after its stated need has been fulfilled. Misuse or distribution of the information contained in this transmission is punishable by civil and/or criminal penalties under state or federal law. If you are not the intended recipient, you are hereby notified that any disclosure, dissemination, saving, printing, copying, or action taken in reliance on the contents of these documents of this message, or any attachment, is strictly prohibited. Please notify the original sender (only) immediately by telephone or by reply e-mail and delete this message, along with any attachments from your computer immediately. ';

            $pdf_content = '';
            $pdf_content .= '<h2>Registration: Submission #'.get_current_user_id().'</h2><br/><br/>';
            $pdf_content .= '<strong>ID: </strong><a href="'.wp_get_attachment_url(get_user_meta(get_current_user_id(),'id_user')[0]).'" target="_blank">Download</a><br/><br/>';
            $pdf_content .= '<strong>Agreement: </strong><a href="'.wp_get_attachment_url(get_user_meta(get_current_user_id(),'agreement_user')[0]).'" target="_blank">Download</a><br/><br/>';
            $pdf_content .= '<strong>CTs Certificate: </strong><a href="'.wp_get_attachment_url(get_user_meta(get_current_user_id(),'cts_certificate_user')[0]).'" target="_blank">Download</a><br/><br/>';
            $pdf_content .= '<strong>First Name: </strong>'.get_user_meta(get_current_user_id(),'firstname_user')[0].'<br/><br/>';
            $pdf_content .= '<strong>Last Name: </strong>'.get_user_meta(get_current_user_id(),'lastname_user')[0].'<br/><br/>';
            $pdf_content .= '<strong>Language: </strong>'.get_user_meta(get_current_user_id(),'language_user')[0].'<br/><br/>';
            $pdf_content .= '<strong>Other Language: </strong>'.get_user_meta(get_current_user_id(),'languageother_user')[0].'<br/><br/>';
            $pdf_content .= '<strong>Contact: </strong>';
            if(!empty(get_user_meta(get_current_user_id(),'city_user')[0])){
                $pdf_content .= get_user_meta(get_current_user_id(),'city_user')[0].'<br/>';
            }
            if(!empty(get_user_meta(get_current_user_id(),'province_user')[0])){
                $pdf_content .= get_user_meta(get_current_user_id(),'province_user')[0].'<br/>';
            }
            if(!empty(get_user_meta(get_current_user_id(),'country_user')[0])){
                $pdf_content .= get_country_name_by_code(get_user_meta(get_current_user_id(),'country_user')[0]).'<br/>';
            }
            if(!empty(get_user_meta(get_current_user_id(),'phone_user')[0])){
                $pdf_content .= get_user_meta(get_current_user_id(),'phone_user')[0].'<br/>';
            }
            $pdf_content .= '<strong>Region: </strong>'.$profile_region.' ('.$profile_state_region.')<br/><br/>';
            $new_region = str_replace(' ','_', $profile_region);
            $file =  send_pdf_email($pdf_content);
            $file_name = 'volunteer_details_'.$new_region.'_'.get_current_user_id() . '.pdf';
            $upload_dir = wp_get_upload_dir()['basedir'];
            $file_path = $upload_dir . '/pdf/'.$file_name;
            file_put_contents($file_path, $file);

            $headers = array('Content-Type: text/html; charset=UTF-8');
            /*foreach ($heads as $head){
                $head_user = new WP_User($head);
                $head_mail = $head_user->user_email;
                wp_mail( $head_mail, $subject, $message_mail, $headers,$file_path);
            }*/

            wp_mail( 'curbelo265@gmail.com', $subject, $message_mail, $headers,$file_path);
            wp_mail( 'regionheads@larkinhospital.com', $subject, $message_mail, $headers,$file_path);
            if(pll_current_language()=='en'):
                echo json_encode( [ "url" => home_url(esc_html('/')).'congratulations', "answer" => 'true', 'message' => '' ] );
                wp_die();
            elseif(pll_current_language()=='hat'):
                echo json_encode( [ "url" => home_url(esc_html('/')).'congratulations-hat', "answer" => 'true', 'message' => '' ] );
                wp_die();
            else:
                echo json_encode( [ "url" => home_url(esc_html('/')).'felicitaciones', "answer" => 'true', 'message' => '' ] );
                wp_die();
            endif;
        }

        if ( ! $land ) {
            echo json_encode( [ "answer"  => 'false',
                'message' => pll__('Could not update profile. Review the required fields.'). ' ' . $message
            ] );
        }
    } else {
        echo json_encode( [ "reply" => 'false', "message" => "You dont have access to this element." ] );
    }
    exit;
}

add_action( 'wp_ajax_make_volunteer1', 'make_volunteer1' );
add_action( 'wp_ajax_nopriv_make_volunteer1', 'make_volunteer1' );
function make_volunteer1() {
    check_ajax_referer( 'update_perfil', 'security' );
    if ( isset( $_POST['data'] ) && is_user_logged_in() ) {
        parse_str( $_POST['data'], $form_data );

        $land    = true;
        $message = '';

        if ( $land && get_user_meta(get_current_user_id(),'volunteer_user')[0]!='yes' ) {
            if(get_user_meta(get_current_user_id(),'volunteer_user')[0]!='yes' && !empty($user_cts_id)){
                if(pll_current_language()=='en'):
                    echo json_encode( [ "url" => home_url(esc_html('/')).'become-a-volunteer', "answer" => 'true', 'message' => 'Profile successfully updated. You can go to the <a href="'.home_url(esc_html('/')).'">home</a> or navigate the site from the main menu.' ] );
                    wp_die();
                elseif(pll_current_language()=='hat'):
                    echo json_encode( [ "url" => home_url(esc_html('/')).'become-a-volunteer-hat', "answer" => 'true', 'message' => 'Profile successfully updated. You can go to the <a href="'.home_url(esc_html('/')).'">home</a> or navigate the site from the main menu.' ] );
                    wp_die();
                else:
                    echo json_encode( [ "url" => home_url(esc_html('/')).'hacerme-voluntario', "answer" => 'true', 'message' => 'Profile successfully updated. You can go to the <a href="'.home_url(esc_html('/')).'">home</a> or navigate the site from the main menu.' ] );
                    wp_die();
                endif;
            }else{
                echo json_encode( [ "url" => '', "answer" => 'true', 'message' => 'Profile successfully updated. You can go to the <a href="'.home_url(esc_html('/')).'">home</a> or navigate the site from the main menu.' ] );
                wp_die();
            }



        }

        if ( ! $land ) {
            echo json_encode( [ "answer"  => 'false',
                'message' => $message
            ] );
        }
    } else {
        echo json_encode( [ "reply" => 'false', "message" => "You dont have access to this element." ] );
    }
    exit;
}

add_action( 'wp_ajax_get_place', 'get_place' );
add_action( 'wp_ajax_nopriv_get_place', 'get_place' );
function get_place(){
    check_ajax_referer( 'update_perfil', 'security' );
    $plazas = sprintf(pll__('%s Plazas'), 0);
    $array = array('count_plazas' => 0, 'text_plazas'=>$plazas);
    if(empty(get_field('cantidad_de_plazas',$_POST['data'])) || get_field('cantidad_de_plazas',$_POST['data'])==0 ){
        echo '';
    }else{
        $array['count_plazas'] = get_field('cantidad_de_plazas',$_POST['data']);
        if(get_field('cantidad_de_plazas',$_POST['data'])==1){
            echo $plazas = sprintf(pll__('%s Plaza'), get_field('cantidad_de_plazas',$_POST['data']));
        }else{
            echo $plazas = sprintf(pll__('%s Plazas'), get_field('cantidad_de_plazas',$_POST['data']));
        }
    }
    exit;
}


add_action( 'wp_ajax_request_place', 'request_place' );
add_action( 'wp_ajax_nopriv_request_place', 'request_place' );
function request_place() {
    check_ajax_referer( 'update_perfil', 'security' );
    if ( isset( $_POST['data'] ) && is_user_logged_in() && get_user_meta(get_current_user_id(),'volunteer_user')[0]=='yes' ) {
        parse_str( $_POST['data'], $form_data );

        $land    = true;
        $message = '';

        $event_id = sanitize_text_field($form_data['event_id']);
        if(empty($event_id)){
            $land = false;
            $message .=  pll__('Event is required'). '. ';
        }else{
            $count_plazas = get_plazas($event_id)['count_plazas'];
            if(empty($count_plazas) || $count_plazas==0){
                $land    = false;
                $message .=  pll__('Places are sold out'). '. ';
            }

            $query    = new WP_Query( array(
                'post_type'      => 'request',
                'posts_per_page' => 999,
                'post_status'    => array('publish','pending'),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'place_request_event',
                        'value'   => $event_id,
                        'compare' => '='
                    ),
                    array(
                        'key'     => 'place_request_volunteer',
                        'value'   => get_current_user_id(),
                        'compare' => '='
                    )
                )
            ) );
            $cantidad = $query->post_count;
            if($cantidad!=0){
                $land    = false;
                $message .=  pll__('You are already registered in that event'). '. ';
            }
        }


        if ( $land ) {
            $post_information = [
                'post_title'   => date('Ymdhi'),
                'post_name'    => date('Ymdhi'),
                'post_status'  => 'pending',
                'post_content' => '',
                'post_type'    => 'request ',
                'post_author'  => get_current_user_id(),
            ];
            $job_id           = wp_insert_post( $post_information );

            update_field( 'place_request_volunteer',  get_current_user_id(), $job_id );
            update_field( 'place_request_event',  $event_id, $job_id );
            $new_count_place = get_field('cantidad_de_plazas',$event_id) - 1;
            update_field( 'cantidad_de_plazas', $new_count_place , $event_id );
            $event_date = get_field('mec_start_date',$event_id);
            $event_zoom_link = get_field('zoom_link',$event_id);
            $event_title = pll__(get_the_title($event_id));
            $event_hora =  get_field('hora',$event_id);
            $event_entrenador =  get_field('event_entrenador', $event_id);
            $user_name = get_user_meta(get_current_user_id(),'firstname_user')[0].' '.get_user_meta(get_current_user_id(),'lastname_user')[0];
            $user_mail = get_userdata(get_current_user_id())->user_email;
            $email_message = '<img src="'.get_template_directory_uri() . '/assets/images/logo-black.jpg"><br/><br/>';
            if(pll_current_language()=='en'):
                $email_message .= get_post_field('post_content',3795);
            elseif(pll_current_language()=='hat'):
                $email_message .= get_post_field('post_content',3797);
            else:
                $email_message .= get_post_field('post_content',3792);
            endif;
            $email_message = str_replace('#event_date', $event_date, $email_message);
            $email_message = str_replace('#event_title', $event_title, $email_message);
            $email_message = str_replace('#user_name', $user_name, $email_message);
            $email_message = str_replace('#user_mail', $user_mail, $email_message);
            $email_message = str_replace('#event_zoom_link', $event_zoom_link, $email_message);
            $email_message = str_replace('#event_hora', $event_hora, $email_message);
            $email_message = str_replace('#event_entrenador', $event_entrenador, $email_message);

            $entrenador_email_message = '<img src="'.get_template_directory_uri() . '/assets/images/logo-black.jpg"><br/><br/>';
            if(pll_current_language()=='en'):
                $entrenador_email_message .= get_post_field('post_content',3819);
            elseif(pll_current_language()=='hat'):
                $entrenador_email_message .= get_post_field('post_content',3820);
            else:
                $entrenador_email_message .= get_post_field('post_content',3817);
            endif;
            $entrenador_email_message = str_replace('#event_date', $event_date, $entrenador_email_message);
            $entrenador_email_message = str_replace('#event_title', $event_title, $entrenador_email_message);
            $entrenador_email_message = str_replace('#user_name', $user_name, $entrenador_email_message);
            $entrenador_email_message = str_replace('#user_mail', $user_mail, $entrenador_email_message);
            $entrenador_email_message = str_replace('#event_zoom_link', $event_zoom_link, $entrenador_email_message);
            $entrenador_email_message = str_replace('#cantidad_plazas', $new_count_place, $entrenador_email_message);
            $entrenador_email_message = str_replace('#event_hora', $event_hora, $entrenador_email_message);
            $entrenador_email_message = str_replace('#event_entrenador', $event_entrenador, $entrenador_email_message);

            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail($user_mail, pll__('Place requested'), $email_message, $headers);
            wp_mail(get_bloginfo('admin_email'), pll__('Place requested'), $email_message, $headers);
            wp_mail(get_bloginfo('admin_email'), pll__('Place requested'), $entrenador_email_message, $headers);
            $args = [
                'role' => 'entrenador',
            ];
            $entrenadores = get_users($args);
            foreach ($entrenadores as $entrenador) {
                wp_mail(get_userdata($entrenador->data->ID)->user_email, pll__('Place requested'), $entrenador_email_message, $headers);
            }
            echo json_encode( [ "answer" => 'true', 'message' => pll__('Place requested') ] );
            wp_die();
        }

        if ( ! $land ) {
            echo json_encode( [ "answer"  => 'false',  'message' => $message ] );
        }
    } else {
        echo json_encode( [ "reply" => 'false', "message" => "You dont have access to this element." ] );
    }
    exit;
}

add_action( 'wp_ajax_create_event', 'create_event' );
add_action( 'wp_ajax_nopriv_create_event', 'create_event' );
function create_event() {
    check_ajax_referer( 'update_perfil', 'security' );
    if ( isset( $_POST['data'] ) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('entrenador', get_current_user_roles())) ) {
        parse_str( $_POST['data'], $form_data );
        $land    = true;
        $message = '';
        $title = sanitize_text_field($form_data['event_type']);
        if (empty($title)) {
            $land = FALSE;
            $message .=  pll__('Event type is required'). '. ';
        }
        $zoom_link = sanitize_text_field($form_data['zoom_link']);
        if (empty($zoom_link)) {
            $land = FALSE;
            $message .=  pll__('Zoom link is required'). '. ';
        }else{
            if (filter_var($zoom_link, FILTER_VALIDATE_URL) == FALSE) {
                $land = FALSE;
                $message .= pll__('Zoom link must be URL'). '. ';
            }
        }
        $event_plazas = sanitize_text_field($form_data['event_plazas']);
        if (empty($event_plazas)) {
            //$land = FALSE;
            //$message .=  pll__('Places is required'). '. ';
        }else{
            if (filter_var($event_plazas, FILTER_VALIDATE_INT) == FALSE) {
                $land = FALSE;
                $message .=  pll__('Places must be numeric'). '. ';
            }
        }
        $event_hora = sanitize_text_field($form_data['hora']);
        if (empty($event_hora)) {
            $land = FALSE;
            $message .=  pll__('Hour is required'). '. ';
        }
        $fecha_evento = sanitize_text_field($form_data['event_date']);
        if (empty($fecha_evento)) {
            $land = FALSE;
            $message .=  pll__('Event date is required'). '. ';
        }
        $event_entrenador = sanitize_text_field($form_data['event_entrenador']);
        if (empty($event_entrenador)) {
            $land = FALSE;
            $message .=  pll__('Coach is required'). '. ';
        }
        if ($land) {
            $post_information = [
                'post_title' => $title,
                'post_name' => $title.'-'.$fecha_evento,
                'post_status' => 'publish',
                'post_content' => '',
                'post_type' => 'mec-events',
                'post_author' => 1,
            ];
            $postID = wp_insert_post($post_information);
            update_field('event_entrenador', $event_entrenador, $postID);
            update_field('hora', $event_hora, $postID);
            update_field('cantidad_de_plazas', $event_plazas, $postID);
            update_field('zoom_link', $zoom_link, $postID);
            update_field('mec_color', '19b3bd', $postID);
            update_field('inline_featured_image', '0', $postID);
            update_field('mec_location_id', '1', $postID);
            update_field('mec_dont_show_map', '1', $postID);
            update_field('mec_organizer_id', '1', $postID);
            update_field('mec_start_date', $fecha_evento, $postID);
            update_field('mec_start_time_hour', '1', $postID);
            update_field('mec_start_time_minutes', '0', $postID);
            update_field('mec_start_time_ampm', 'AM', $postID);
            update_field('mec_start_day_seconds', '3600', $postID);
            update_field('mec_end_date', $fecha_evento, $postID);
            update_field('mec_end_time_hour', '11', $postID);
            update_field('mec_end_time_minutes', '55', $postID);
            update_field('mec_end_time_ampm', 'PM', $postID);
            update_field('mec_end_day_seconds', '86100', $postID);
            update_field('mec_hide_end_time', '0', $postID);
            update_field('mec_hide_time', '0', $postID);
            update_field('mec_allday', '0', $postID);
            global $wpdb;
            $table_name = $wpdb->prefix . "mec_dates";
            $dstart = $fecha_evento;
            $dend = $fecha_evento;
            $tstart = strtotime($dstart.' 01:00:00');
            $tend = strtotime($dend.' 23:55:00');
            $results = $wpdb->get_results($wpdb->prepare("INSERT INTO $table_name  (`id`, `post_id`, `dstart`, `dend`, `tstart`, `tend`) VALUES (NULL, '$postID' , '$dstart' , '$dend' , '$tstart','$tend');"));
            $table_name = $wpdb->prefix . "mec_events";
            $results = $wpdb->get_results($wpdb->prepare("INSERT INTO $table_name  (`id`, `post_id`, `start`, `end`, `repeat`, `time_start`, `time_end`) VALUES (NULL, '$postID' , '$dstart' , $dstart , '0','3600','86100');"));
            echo json_encode(["answer" => 'true', 'message' => pll__('Event created') ]);
            wp_die();
        }
        if ( ! $land ) {
            echo json_encode( [ "answer"  => 'false',  'message' => $message ] );
            wp_die();
        }
    } else {
        echo json_encode( [ "reply" => 'false', "message" => "You dont have access to this element." ] );
        wp_die();
    }
    exit;
}

add_action( 'wp_ajax_edit_event', 'edit_event' );
add_action( 'wp_ajax_nopriv_edit_event', 'edit_event' );
function edit_event() {
    check_ajax_referer( 'update_perfil', 'security' );
    if ( isset( $_POST['data'] ) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('entrenador', get_current_user_roles())) ) {
        parse_str( $_POST['data'], $form_data );
        $land    = true;
        $message = '';
        $title = sanitize_text_field($form_data['event_type']);
        if (empty($title)) {
            $land = FALSE;
            $message .=  pll__('Event type is required'). '. ';
        }
        $zoom_link = sanitize_text_field($form_data['zoom_link']);
        if (empty($zoom_link)) {
            $land = FALSE;
            $message .=  pll__('Zoom link is required'). '. ';
        }else{
            if (filter_var($zoom_link, FILTER_VALIDATE_URL) == FALSE) {
                $land = FALSE;
                $message .= pll__('Zoom link must be URL'). '. ';
            }
        }
        $event_hora = sanitize_text_field($form_data['hora']);
        if (empty($event_hora)) {
            $land = FALSE;
            $message .=  pll__('Hour is required'). '. ';
        }
        $event_entrenador = sanitize_text_field($form_data['event_entrenador']);
        if (empty($event_entrenador)) {
            $land = FALSE;
            $message .=  pll__('Coach is required'). '. ';
        }

        $event_plazas = sanitize_text_field($form_data['event_plazas']);
        if (empty($event_plazas)) {
            //$land = FALSE;
            //$message .=  pll__('Places is required'). '. ';
        }else{
            if (filter_var($event_plazas, FILTER_VALIDATE_INT) == FALSE) {
                $land = FALSE;
                $message .=  pll__('Places must be numeric'). '. ';
            }
        }
        $fecha_evento = sanitize_text_field($form_data['event_date']);
        if (empty($fecha_evento)) {
            $land = FALSE;
            $message .=  pll__('Event date is required'). '. ';
        }
        $event_id = sanitize_text_field($form_data['event_id']);
        if ($land) {
            $my_post = array(
                'ID'           => $event_id,
                'post_title'   => $title,
                'post_content' => '',
            );
            wp_update_post( $my_post );
            update_field('event_entrenador', $event_entrenador, $event_id);
            update_field('hora', $event_hora, $event_id);
            update_field('cantidad_de_plazas', $event_plazas, $event_id);
            update_field('zoom_link', $zoom_link, $event_id);
            update_field('mec_color', '19b3bd', $event_id);
            update_field('inline_featured_image', '0', $event_id);
            update_field('mec_location_id', '1', $event_id);
            update_field('mec_dont_show_map', '1', $event_id);
            update_field('mec_organizer_id', '1', $event_id);
            update_field('mec_start_date', $fecha_evento, $event_id);
            update_field('mec_start_time_hour', '1', $event_id);
            update_field('mec_start_time_minutes', '0', $event_id);
            update_field('mec_start_time_ampm', 'AM', $event_id);
            update_field('mec_start_day_seconds', '3600', $event_id);
            update_field('mec_end_date', $fecha_evento, $event_id);
            update_field('mec_end_time_hour', '11', $event_id);
            update_field('mec_end_time_minutes', '55', $event_id);
            update_field('mec_end_time_ampm', 'PM', $event_id);
            update_field('mec_end_day_seconds', '86100', $event_id);
            update_field('mec_hide_end_time', '0', $event_id);
            update_field('mec_hide_time', '0', $event_id);
            update_field('mec_allday', '0', $event_id);
            global $wpdb;
            $table_name = $wpdb->prefix . "mec_dates";
            $dstart = $fecha_evento;
            $dend = $fecha_evento;
            $tstart = strtotime($dstart.' 01:00:00');
            $tend = strtotime($dend.' 23:55:00');
            $results = $wpdb->get_results($wpdb->prepare("UPDATE $table_name SET `dstart` = '$dstart',`dend` = '$dend', `tstart` = '$tstart', `tend` = '$tend' WHERE $table_name.`post_id` = '$event_id';"));
            $table_name = $wpdb->prefix . "mec_events";

            $results = $wpdb->get_results($wpdb->prepare("UPDATE $table_name SET `start` = '$dstart', `time_end` = '3600',`time_start` = '86100' , `end` = '$dend' WHERE $table_name.`post_id` = '$event_id';"));
            echo json_encode(["answer" => 'true', 'message' => pll__('Event updated') ]);
            wp_die();
        }
        if ( ! $land ) {
            echo json_encode( [ "answer"  => 'false',  'message' => $message ] );
            wp_die();
        }
    } else {
        echo json_encode( [ "reply" => 'false', "message" => "You dont have access to this element." ] );
        wp_die();
    }
    exit;
}

add_action( 'wp_ajax_delete_event', 'delete_event' );
add_action( 'wp_ajax_nopriv_delete_event', 'delete_event' );
function delete_event() {
    check_ajax_referer( 'update_perfil', 'security' );
    if ( isset( $_POST['data'] ) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('entrenador', get_current_user_roles())) ) {
        parse_str( $_POST['data'], $form_data );

        $land    = true;
        $message = '';

        $event_id = sanitize_text_field($form_data['event_id']);

        if ( !empty($event_id) ) {
            wp_delete_post($event_id);
            echo json_encode( [ "answer" => 'true', 'message' => pll__('Event deleted') ] );
            wp_die();
        }

        if ( ! $land ) {
            echo json_encode( [ "answer"  => 'false',  'message' => 'Error.' ] );
        }
    } else {
        echo json_encode( [ "reply" => 'false', "message" => "Error." ] );
    }
    exit;
}

add_action( 'wp_ajax_delete_requests', 'delete_requests' );
add_action( 'wp_ajax_nopriv_delete_requests', 'delete_requests' );
function delete_requests() {
    check_ajax_referer( 'update_perfil', 'security' );
    if ( isset( $_POST['data'] ) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('entrenador', get_current_user_roles())) ) {
        parse_str( $_POST['data'], $form_data );

        $land    = true;
        $message = '';

        $event_id = sanitize_text_field($form_data['event_id']);
        $user_id = get_field('place_request_volunteer',$event_id);
        $user_mail = get_userdata($user_id)->user_email;
        $user_name = get_user_meta($user_id,'firstname_user')[0].' '.get_user_meta($user_id,'lastname_user')[0];

        $event_date = get_field('mec_start_date', $event_id);
        $event_zoom_link = get_field('zoom_link', $event_id);
        $event_title = pll__(get_the_title($event_id));
        $event_hora = get_field('hora', $event_id);
        $event_entrenador = get_field('event_entrenador', $event_id);

        $email_message = '<img src="'.get_template_directory_uri() . '/assets/images/logo-black.jpg"><br/><br/>';
        if(pll_current_language()=='en'):
            $email_message .= get_post_field('post_content',3955);
        elseif(pll_current_language()=='hat'):
            $email_message .= get_post_field('post_content',3957);
        else:
            $email_message .= get_post_field('post_content',3953);
        endif;
        $email_message = str_replace('#event_date', $event_date, $email_message);
        $email_message = str_replace('#event_title', $event_title, $email_message);
        $email_message = str_replace('#user_name', $user_name, $email_message);
        $email_message = str_replace('#user_mail', $user_mail, $email_message);
        $email_message = str_replace('#event_zoom_link', $event_zoom_link, $email_message);
        $email_message = str_replace('#event_hora', $event_hora, $email_message);
        $email_message = str_replace('#event_entrenador', $event_entrenador, $email_message);

        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($user_mail, pll__('Request deleted'), $email_message, $headers);
        wp_mail(get_bloginfo('admin_email'), pll__('Request deleted'), $email_message, $headers);

        if ( !empty($event_id) ) {
            wp_delete_post($event_id);
            echo json_encode( [ "answer" => 'true', 'message' => pll__('Request deleted') ] );
            wp_die();
        }

        if ( ! $land ) {
            echo json_encode( [ "answer"  => 'false',  'message' => 'Error.' ] );
        }
    } else {
        echo json_encode( [ "reply" => 'false', "message" => "Error." ] );
    }
    exit;
}

add_action( 'wp_ajax_edit_request', 'edit_request' );
add_action( 'wp_ajax_nopriv_edit_request', 'edit_request' );
function edit_request() {
    check_ajax_referer( 'update_perfil', 'security' );
    if ( isset( $_POST['data'] ) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('entrenador', get_current_user_roles())) ) {
        parse_str( $_POST['data'], $form_data );
        $land    = true;
        $message = '';
        $event_id = $form_data['event_id'];
        $user_id =  sanitize_text_field($form_data['user_id']);
        $request_id =  sanitize_text_field($form_data['request_id']);
        $user_mail = get_userdata($user_id)->user_email;
        $user_name = get_user_meta($user_id,'firstname_user')[0].' '.get_user_meta($user_id,'lastname_user')[0];

        $event_date = get_field('mec_start_date', $event_id);
        $event_zoom_link = get_field('zoom_link', $event_id);
        $event_title = pll__(get_the_title($event_id));
        $event_hora = get_field('hora', $event_id);
        $event_entrenador = get_field('event_entrenador', $event_id);

        $email_message = '<img src="'.get_template_directory_uri() . '/assets/images/logo-black.jpg"><br/><br/>';
        if(pll_current_language()=='en'):
            $email_message .= get_post_field('post_content',3949);
        elseif(pll_current_language()=='hat'):
            $email_message .= get_post_field('post_content',3951);
        else:
            $email_message .= get_post_field('post_content',3945);
        endif;
        $email_message = str_replace('#event_date', $event_date, $email_message);
        $email_message = str_replace('#event_title', $event_title, $email_message);
        $email_message = str_replace('#user_name', $user_name, $email_message);
        $email_message = str_replace('#user_mail', $user_mail, $email_message);
        $email_message = str_replace('#event_zoom_link', $event_zoom_link, $email_message);
        $email_message = str_replace('#event_hora', $event_hora, $email_message);
        $email_message = str_replace('#event_entrenador', $event_entrenador, $email_message);

        $email_admin_message = '<img src="'.get_template_directory_uri() . '/assets/images/logo-black.jpg"><br/><br/>';
        if(pll_current_language()=='en'):
            $email_admin_message .= get_post_field('post_content',3961);
        elseif(pll_current_language()=='hat'):
            $email_admin_message .= get_post_field('post_content',3962);
        else:
            $email_admin_message .= get_post_field('post_content',3959);
        endif;
        $email_admin_message = str_replace('#event_date', $event_date, $email_admin_message);
        $email_admin_message = str_replace('#event_title', $event_title, $email_admin_message);
        $email_admin_message = str_replace('#user_name', $user_name, $email_admin_message);
        $email_admin_message = str_replace('#user_mail', $user_mail, $email_admin_message);
        $email_admin_message = str_replace('#event_zoom_link', $event_zoom_link, $email_admin_message);
        $email_admin_message = str_replace('#event_hora', $event_hora, $email_admin_message);
        $email_admin_message = str_replace('#event_entrenador', $event_entrenador, $email_admin_message);
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($user_mail, pll__('Request updated'), $email_message, $headers);
        wp_mail(get_bloginfo('admin_email'), pll__('Request updated'), $email_message, $headers);
        $args = [
            'role' => 'entrenador',
        ];
        $entrenadores = get_users($args);
        foreach ($entrenadores as $entrenador) {
            wp_mail(get_userdata($entrenador->data->ID)->user_email, pll__('Request updated'), $email_admin_message, $headers);
        }

        if ($land) {
            $my_post = array(
                'ID'           => $request_id,
                'post_title'   => get_the_title($request_id),
                'post_content' => '',
            );
            wp_update_post( $my_post );
            update_field('place_request_event', $event_id, $request_id);
            update_field('place_request_volunteer', $user_id, $request_id);
            echo json_encode(["answer" => 'true', 'message' => pll__('Request updated') ]);
            wp_die();
        }
        if ( ! $land ) {
            echo json_encode( [ "answer"  => 'false',  'message' => $message ] );
            wp_die();
        }
    } else {
        echo json_encode( [ "reply" => 'false', "message" => "You dont have access to this element." ] );
        wp_die();
    }
    exit;
}

function get_country_name_by_code($code){
    $name = '';
    $list = array("AF" => "Afghanistan", "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antarctica", "AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "AX" => "Åland Islands", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "BQ" => "British Antarctic Territory", "IO" => "British Indian Ocean Territory", "VG" => "British Virgin Islands", "BN" => "Brunei", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "CT" => "Canton and Enderbury Islands", "CV" => "Cape Verde", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos [Keeling] Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo - Brazzaville", "CD" => "Congo - Kinshasa", "CK" => "Cook Islands", "CR" => "Costa Rica", "HR" => "Croatia", "CU" => "Cuba", "CY" => "Cyprus", "CZ" => "Czech Republic", "CI" => "Côte d’Ivoire", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "NQ" => "Dronning Maud Land", "DD" => "East Germany", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "ET" => "Ethiopia", "FK" => "Falkland Islands", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "FQ" => "French Southern and Antarctic Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GN" => "Guinea", "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard Island and McDonald Islands", "HN" => "Honduras", "HK" => "Hong Kong SAR China", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran", "IQ" => "Iraq", "IE" => "Ireland", "IM" => "Isle of Man", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "JE" => "Jersey", "JT" => "Johnston Island", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KW" => "Kuwait", "KG" => "Kyrgyzstan", "LA" => "Laos", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libya", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macau SAR China", "MK" => "Macedonia", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "FX" => "Metropolitan France", "MX" => "Mexico", "FM" => "Micronesia", "MI" => "Midway Islands", "MD" => "Moldova", "MC" => "Monaco", "MN" => "Mongolia", "ME" => "Montenegro", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar [Burma]", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "AN" => "Netherlands Antilles", "NT" => "Neutral Zone", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "KP" => "North Korea", "VD" => "North Vietnam", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PC" => "Pacific Islands Trust Territory", "PK" => "Pakistan", "PW" => "Palau", "PS" => "Palestinian Territories", "PA" => "Panama", "PZ" => "Panama Canal Zone", "PG" => "Papua New Guinea", "PY" => "Paraguay", "YD" => "People's Democratic Republic of Yemen", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn Islands", "PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RO" => "Romania", "RU" => "Russia", "RW" => "Rwanda", "RE" => "Réunion", "BL" => "Saint Barthélemy", "SH" => "Saint Helena", "KN" => "Saint Kitts and Nevis", "LC" => "Saint Lucia", "MF" => "Saint Martin", "PM" => "Saint Pierre and Miquelon", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino", "SA" => "Saudi Arabia", "SN" => "Senegal", "RS" => "Serbia", "CS" => "Serbia and Montenegro", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia and the South Sandwich Islands", "KR" => "South Korea", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard and Jan Mayen", "SZ" => "Swaziland", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syria", "ST" => "São Tomé and Príncipe", "TW" => "Taiwan", "TJ" => "Tajikistan", "TZ" => "Tanzania", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo", "TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks and Caicos Islands", "TV" => "Tuvalu", "UM" => "U.S. Minor Outlying Islands", "PU" => "U.S. Miscellaneous Pacific Islands", "VI" => "U.S. Virgin Islands", "UG" => "Uganda", "UA" => "Ukraine", "SU" => "Union of Soviet Socialist Republics", "AE" => "United Arab Emirates", "GB" => "United Kingdom", "US" => "United States", "ZZ" => "Unknown or Invalid Region", "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VA" => "Vatican City", "VE" => "Venezuela", "VN" => "Vietnam", "WK" => "Wake Island", "WF" => "Wallis and Futuna", "EH" => "Western Sahara", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe");
    $name .= $list[$code];
    return $name;
}

function get_country_code_by_name($code){
    $name = '';
    $list = array("AF" => "Afghanistan", "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antarctica", "AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "AX" => "Åland Islands", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "BQ" => "British Antarctic Territory", "IO" => "British Indian Ocean Territory", "VG" => "British Virgin Islands", "BN" => "Brunei", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "CT" => "Canton and Enderbury Islands", "CV" => "Cape Verde", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos [Keeling] Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo - Brazzaville", "CD" => "Congo - Kinshasa", "CK" => "Cook Islands", "CR" => "Costa Rica", "HR" => "Croatia", "CU" => "Cuba", "CY" => "Cyprus", "CZ" => "Czech Republic", "CI" => "Côte d’Ivoire", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "NQ" => "Dronning Maud Land", "DD" => "East Germany", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "ET" => "Ethiopia", "FK" => "Falkland Islands", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "FQ" => "French Southern and Antarctic Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GN" => "Guinea", "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard Island and McDonald Islands", "HN" => "Honduras", "HK" => "Hong Kong SAR China", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran", "IQ" => "Iraq", "IE" => "Ireland", "IM" => "Isle of Man", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "JE" => "Jersey", "JT" => "Johnston Island", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KW" => "Kuwait", "KG" => "Kyrgyzstan", "LA" => "Laos", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libya", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macau SAR China", "MK" => "Macedonia", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "FX" => "Metropolitan France", "MX" => "Mexico", "FM" => "Micronesia", "MI" => "Midway Islands", "MD" => "Moldova", "MC" => "Monaco", "MN" => "Mongolia", "ME" => "Montenegro", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar [Burma]", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "AN" => "Netherlands Antilles", "NT" => "Neutral Zone", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "KP" => "North Korea", "VD" => "North Vietnam", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PC" => "Pacific Islands Trust Territory", "PK" => "Pakistan", "PW" => "Palau", "PS" => "Palestinian Territories", "PA" => "Panama", "PZ" => "Panama Canal Zone", "PG" => "Papua New Guinea", "PY" => "Paraguay", "YD" => "People's Democratic Republic of Yemen", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn Islands", "PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RO" => "Romania", "RU" => "Russia", "RW" => "Rwanda", "RE" => "Réunion", "BL" => "Saint Barthélemy", "SH" => "Saint Helena", "KN" => "Saint Kitts and Nevis", "LC" => "Saint Lucia", "MF" => "Saint Martin", "PM" => "Saint Pierre and Miquelon", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino", "SA" => "Saudi Arabia", "SN" => "Senegal", "RS" => "Serbia", "CS" => "Serbia and Montenegro", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia and the South Sandwich Islands", "KR" => "South Korea", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard and Jan Mayen", "SZ" => "Swaziland", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syria", "ST" => "São Tomé and Príncipe", "TW" => "Taiwan", "TJ" => "Tajikistan", "TZ" => "Tanzania", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo", "TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks and Caicos Islands", "TV" => "Tuvalu", "UM" => "U.S. Minor Outlying Islands", "PU" => "U.S. Miscellaneous Pacific Islands", "VI" => "U.S. Virgin Islands", "UG" => "Uganda", "UA" => "Ukraine", "SU" => "Union of Soviet Socialist Republics", "AE" => "United Arab Emirates", "GB" => "United Kingdom", "US" => "United States", "ZZ" => "Unknown or Invalid Region", "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VA" => "Vatican City", "VE" => "Venezuela", "VN" => "Vietnam", "WK" => "Wake Island", "WF" => "Wallis and Futuna", "EH" => "Western Sahara", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe");
    foreach ($list as $key=>$country){
        if($country==$code): $name = $key; endif;
    }
    return $name;
}

use Dompdf\Dompdf;
function send_pdf_email($body)
{
    include 'vendors/dompdf/autoload.inc.php';
    $dompdf = new Dompdf();

    $dompdf->loadHtml($body);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    return $dompdf->output();
}

function generate_certificate_pdf($name, $date,$language)
{
    $name = $name;
    $date = $date;
    $language = $language;
    $url = site_url().'/wp-content/themes/volunteer/vendors/demo.php';

    $postfields = array('name' => $name,        'date' => $date,        'language' => $language);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch);
    $file = $result;
    $file_name = md5(rand()) . '.pdf';
    $upload_dir = wp_get_upload_dir()['basedir'];
    $file_path = $upload_dir . '/pdf/'.$file_name;
    file_put_contents($file_path, $file);
    $uploaddir = wp_upload_dir();
    $uploadfile = $uploaddir['path'] . '/' . $file_name;

    $contents= file_get_contents(site_url().'/wp-content/uploads/pdf/'.$file_name);
    $savefile = fopen($uploadfile, 'w');
    fwrite($savefile, $contents);
    fclose($savefile);

    $wp_filetype = wp_check_filetype(basename($file_name), null );

    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => $file_name,
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file_path );
    return $attach_id;
}

function get_heads_by_region($region){
    $array = array();
    $users = new WP_User_Query(array(
        'meta_query' => array(
            'relation' => 'OR',
            'role' => 'boss',
            'number' => 99999999,
            array(
                'key' => 'region_user',
                'value' => $region,
                'compare' => '='
            )
        )
    ));
    $users_found = $users->get_results();
    foreach ($users_found as $user):
        $array[] = $user->ID;
    endforeach;
    return $array;
}

function get_volunteers_by_region($region){
    $array = array();
    $users = new WP_User_Query(array(
        'meta_query' => array(
            'relation' => 'OR',
            'role' => 'boss',
            'number' => 99999999,
            array(
                'key' => 'region_user',
                'value' => $region,
                'compare' => '='
            )
        )
    ));
    $users_found = $users->get_results();
    foreach ($users_found as $user):
        if($user->ID!=get_current_user_id()){
            $array[] = $user->ID;
        }
    endforeach;
    return $array;
}

function wpa_sideload_file( $file, $post_id = 0, $desc = null ) {
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    if( empty( $file ) ) {
        return new \WP_Error( 'error', 'File is empty' );
    }

    $file_array = array();

    // Get filename and store it into $file_array
    // Add more file types if necessary
    preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png|pdf)\b/i', $file, $matches );
    $file_array['name'] = basename( $matches[0] );

    // Download file into temp location.
    $file_array['tmp_name'] = download_url( $file );

    // If error storing temporarily, return the error.
    if ( is_wp_error( $file_array['tmp_name'] ) ) {
        return new \WP_Error( 'error', 'Error while storing file temporarily' );
    }

    // Store and validate
    $id = media_handle_sideload( $file_array, $post_id, $desc );

    // Unlink if couldn't store permanently
    if ( is_wp_error( $id ) ) {
        unlink( $file_array['tmp_name'] );
        return new \WP_Error( 'error', "Couldn't store upload permanently" );
    }

    if ( empty( $id ) ) {
        return new \WP_Error( 'error', "Upload ID is empty" );
    }
    return $id;
}

function initial_volunteers() {

    $args = [
        'order' => 'ASC',
        'orderby' => 'display_name',
        'number' => 999999,
        'offset' => 0,
    ];

    $args['meta_query'][] =
        [
            'key' => 'volunteer_user',
            'value' => 'yes',
            'compare' => '=',
        ];

    /*if (!in_array('administrator', get_current_user_roles())):
        $args['meta_query'][] =
            [
                'key' => 'region_user',
                'value' => get_user_meta(get_current_user_id(),'region_user')[0],
                'compare' => '=',
            ];
    endif;*/

    $output = "";
    $wp_user_query = new WP_User_Query($args);
    $authors = $wp_user_query->get_results();

    if (!empty($authors)) :
        $output .= '<div class="tr d-flex">
                                                    <div class="td td-volunteer-check"><div class="td-padding"><input name="select_all" class="select_all" type="checkbox" value="1"/></div></div>
                                                    <div class="td td-20"><div class="td-padding">'.pll__('Name').'</div></div>
                                                    <div class="td td-10"><div class="td-padding">'.pll__('Region').'</div></div>
                                                    <div class="td td-25"><div class="td-padding">'.pll__('Email').'</div></div>
                                                    <div class="td td-10"><div class="td-padding">'.pll__('Language').'</div></div>
                                                    <div class="td td-15"><div class="td-padding">'.pll__('Entrenado').'</div></div>
                                                    <div class="td td-15"><div class="td-padding">'.pll__('Checked').'</div></div>
                                                    </div>';
        foreach ($authors as $author):
            $current_user = $author->ID;

            $name = get_user_meta($current_user,'firstname_user')[0].' '.get_user_meta($current_user,'lastname_user')[0];
            $language = get_user_meta($current_user,'language_user')[0];
            $language_other = get_user_meta($current_user,'languageother_user')[0];
            $mail = '<a href="mailto:'.$author->user_email.'">'.$author->user_email.'</a>';
            $region = get_user_meta($current_user,'region_user')[0];

            $id = '<a href="'.wp_get_attachment_url(get_user_meta($current_user,'id_user')[0]).'" target="_blank">Download</a>';
            $agreement = '<a href="'.wp_get_attachment_url(get_user_meta($current_user,'agreement_user')[0]).'" target="_blank">Download</a>';
            $certificate = '<a href="'.wp_get_attachment_url(get_user_meta($current_user,'cts_certificate_user')[0]).'" target="_blank">Download</a>';
            $city = get_user_meta($current_user,'city_user')[0];
            $province_user = get_user_meta($current_user,'province_user')[0];
            $country_user = get_user_meta($current_user,'country_user')[0];
            $phone_user = '<a href="tel:'.get_user_meta($current_user,'phone_user')[0].'">'.get_user_meta($current_user,'phone_user')[0].'</a>';

            $status = get_user_meta($current_user,'checked_user')[0];
            $date_status = get_user_meta($current_user,'date_checked_user')[0];
            $user_status = get_user_meta($current_user,'user_checked_user')[0];
            if(!empty($user_status)){
                $user_status = get_user_meta($user_status,'firstname_user')[0].' '.get_user_meta($user_status,'lastname_user')[0];
            }else{
                $user_status = '';
            }
            if($status=='yes'){
                $check_status_svg = '<svg style="margin-right:15px;"  width="18px" height="18px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 375.147 375.147" style="enable-background:new 0 0 375.147 375.147;" xml:space="preserve"><g><g><polygon fill="#19b3bd" points="344.96,44.48 119.147,270.293 30.187,181.333 0,211.52 119.147,330.667 375.147,74.667"/></g></g></svg>';
                $fecha  = substr($date_status,6,2).'-'.substr($date_status,4,2).'-'.substr($date_status,0,4);
                $check_status = $user_status.' ( '.$fecha.' )';
            }else{
                $check_status_svg = '';
                $check_status = '';
            }

            $entrenado = get_user_meta($current_user,'entrenado_user')[0];
            $date_entrenado = get_user_meta($current_user,'date_entrenado_user')[0];
            $user_entrenado = get_user_meta($current_user,'user_entrenado_user')[0];
            if(!empty($user_entrenado)){
                $user_entrenado = get_user_meta($user_entrenado,'firstname_user')[0].' '.get_user_meta($user_entrenado,'lastname_user')[0];
            }else{
                $user_entrenado = '';
            }
            if($entrenado=='yes'){
                $check_entrenado_svg = '<svg width="18px" height="18px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 375.147 375.147" style="enable-background:new 0 0 375.147 375.147;" xml:space="preserve"><g><g><polygon fill="#019946" points="344.96,44.48 119.147,270.293 30.187,181.333 0,211.52 119.147,330.667 375.147,74.667"/></g></g></svg>';
                $fecha  = substr($date_entrenado,6,2).'-'.substr($date_entrenado,4,2).'-'.substr($date_entrenado,0,4);
                $check_entrenado = $user_entrenado.' ( '.$fecha.' )';
            }else{
                $check_entrenado_svg = '';
                $check_entrenado = '';
            }

            $output .= '<div class="tr d-flex">';
            $output .= '<div class="td td-volunteer-check"><div class="td-padding"><input name="select_checked[]" class="select_checked" type="checkbox" value="' . $current_user . '"/></div></div>';
            $output .= '<div class="td td-20"><div class="td-padding td-padding-name">'.$name.'</div></div>';
            $output .= '<div class="td td-10"><div class="td-padding">'.$region.'</div></div>';
            $output .= '<div class="td td-25"><div class="td-padding">'.$mail.'</div></div>';
            $output .= '<div class="td td-10"><div class="td-padding">'.$language.'</div></div>';
            $output .= '<div class="td td-15"><div class="td-padding">'.$check_entrenado_svg.'</div></div>';
            $output .= '<div class="td td-15"><div class="td-padding">'.$check_status_svg.'</div></div>';
            $output .= '<div class="td td-100 volunteer-td-all-data"><div class="td td-100 d-flex flex-wrap">';
            $output .= '<div class="td td-20"><div class="td-padding"><span class="label">Other language: </span>'.$language_other.'</div></div>';
            $output .= '<div class="td td-10"><div class="td-padding"><span class="label">ID: </span>'.$id.'</div></div>';
            $output .= '<div class="td td-10"><div class="td-padding"><span class="label">Agreement: </span>'.$agreement.'</div></div>';
            $output .= '<div class="td td-10"><div class="td-padding"><span class="label">Certificate: </span>'.$certificate.'</div></div>';
            if(!empty($check_status)){
                $output .= '<div class="td td-25"><div class="td-padding"><span class="label">'.pll__('Checked').': </span>'.$check_status.'</div></div>';
            }
            if(!empty($check_entrenado)){
                $output .= '<div class="td td-25"><div class="td-padding"><span class="label">'.pll__('Entrenado').': </span>'.$check_entrenado.'</div></div>';
            }
            $output .= '</div></div>';
            $output .= '</div>';
        endforeach;
    else:
        $output = pll__('Not found volunteers.');
    endif;
    return $output;
}

add_action('wp_ajax_load_volunteers', 'load_volunteers');
add_action('wp_ajax_nopriv_load_volunteers', 'load_volunteers');
function load_volunteers() {
    check_ajax_referer( 'update_perfil', 'security' );
    if (isset($_POST['data']) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles())) :
        parse_str($_POST['data'], $form_data);
        $volunteers_search_text = $form_data['volunteers_search_text'];
        $volunteers_search_region = $form_data['volunteers_search_region'];
        $volunteers_search_status = $form_data['volunteers_search_status'];
        $volunteers_search_entrenado = $form_data['volunteers_search_entrenado'];
        $output = '';

        $args = [
            'order' => 'ASC',
            'orderby' => 'display_name',
            'number' => 999999,
            'offset' => 0,
        ];

        $args['meta_query'][] =
            [
                'key' => 'volunteer_user',
                'value' => 'yes',
                'compare' => '=',
            ];

        if (!empty($volunteers_search_region)) {
            $args['meta_query'][] =
                [
                    'key' => 'region_user',
                    'value' => $volunteers_search_region,
                    'compare' => '=',
                ];
        }

        if ($volunteers_search_status=='yes' || $volunteers_search_status=='no') {
            $args['meta_query'][] =
                [
                    'key' => 'checked_user',
                    'value' => $volunteers_search_status,
                    'compare' => '=',
                ];
        }
        if ($volunteers_search_entrenado=='yes' || $volunteers_search_entrenado=='no') {
            $args['meta_query'][] =
                [
                    'key' => 'entrenado_user',
                    'value' => $volunteers_search_entrenado,
                    'compare' => '=',
                ];
        }



        if (!empty($volunteers_search_text)) {
            $args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key' => 'firstname_user',
                    'value' => $volunteers_search_text,
                    'compare' => 'LIKE',
                ],
                [
                    'key' => 'lastname_user',
                    'value' => $volunteers_search_text,
                    'compare' => 'LIKE',
                ],
            ];
        }

        $wp_user_query = new WP_User_Query($args);
        $authors = $wp_user_query->get_results();
        if (!empty($authors)) :
            $output .= '<div class="tr d-flex">
                                                    <div class="td td-volunteer-check"><div class="td-padding"><input name="select_all" class="select_all" type="checkbox" value="1"/></div></div>
                                                    <div class="td td-20"><div class="td-padding">'.pll__('Name').'</div></div>
                                                    <div class="td td-10"><div class="td-padding">'.pll__('Region').'</div></div>
                                                    <div class="td td-25"><div class="td-padding">'.pll__('Email').'</div></div>
                                                    <div class="td td-10"><div class="td-padding">'.pll__('Language').'</div></div>
                                                    <div class="td td-15"><div class="td-padding">'.pll__('Entrenado').'</div></div>
                                                    <div class="td td-15"><div class="td-padding">'.pll__('Checked').'</div></div>
                                                    </div>';
            foreach ($authors as $author):

                $current_user = $author->ID;

                $name = get_user_meta($current_user,'firstname_user')[0].' '.get_user_meta($current_user,'lastname_user')[0];
                $language = get_user_meta($current_user,'language_user')[0];
                $language_other = get_user_meta($current_user,'languageother_user')[0];
                $mail = '<a href="mailto:'.$author->user_email.'">'.$author->user_email.'</a>';
                $region = get_user_meta($current_user,'region_user')[0];

                $id = '<a href="'.wp_get_attachment_url(get_user_meta($current_user,'id_user')[0]).'" target="_blank">Download</a>';
                $agreement = '<a href="'.wp_get_attachment_url(get_user_meta($current_user,'agreement_user')[0]).'" target="_blank">Download</a>';
                $certificate = '<a href="'.wp_get_attachment_url(get_user_meta($current_user,'cts_certificate_user')[0]).'" target="_blank">Download</a>';
                $city = get_user_meta($current_user,'city_user')[0];
                $province_user = get_user_meta($current_user,'province_user')[0];
                $country_user = get_user_meta($current_user,'country_user')[0];
                $phone_user = '<a href="tel:'.get_user_meta($current_user,'phone_user')[0].'">'.get_user_meta($current_user,'phone_user')[0].'</a>';

                $status = get_user_meta($current_user,'checked_user')[0];
                $date_status = get_user_meta($current_user,'date_checked_user')[0];
                $user_status = get_user_meta($current_user,'user_checked_user')[0];
                if(!empty($user_status)){
                    $user_status = get_user_meta($user_status,'firstname_user')[0].' '.get_user_meta($user_status,'lastname_user')[0];
                }else{
                    $user_status = '';
                }
                if($status=='yes'){
                    $check_status_svg = '<svg style="margin-right:15px;" width="18px" height="18px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 375.147 375.147" style="enable-background:new 0 0 375.147 375.147;" xml:space="preserve"><g><g><polygon fill="#19b3bd" points="344.96,44.48 119.147,270.293 30.187,181.333 0,211.52 119.147,330.667 375.147,74.667"/></g></g></svg>';
                    $fecha  = substr($date_status,6,2).'-'.substr($date_status,4,2).'-'.substr($date_status,0,4);
                    $check_status = $user_status.' ( '.$fecha.' )';
                }else{
                    $check_status_svg = '';
                    $check_status = '';
                }

                $entrenado = get_user_meta($current_user,'entrenado_user')[0];
                $date_entrenado = get_user_meta($current_user,'date_entrenado_user')[0];
                $user_entrenado = get_user_meta($current_user,'user_entrenado_user')[0];
                if(!empty($user_entrenado)){
                    $user_entrenado = get_user_meta($user_entrenado,'firstname_user')[0].' '.get_user_meta($user_entrenado,'lastname_user')[0];
                }else{
                    $user_entrenado = '';
                }
                if($entrenado=='yes'){
                    $check_entrenado_svg = '<svg width="18px" height="18px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 375.147 375.147" style="enable-background:new 0 0 375.147 375.147;" xml:space="preserve"><g><g><polygon fill="#019946" points="344.96,44.48 119.147,270.293 30.187,181.333 0,211.52 119.147,330.667 375.147,74.667"/></g></g></svg>';
                    $fecha  = substr($date_entrenado,6,2).'-'.substr($date_entrenado,4,2).'-'.substr($date_entrenado,0,4);
                    $check_entrenado = $user_entrenado.' ( '.$fecha.' )';
                }else{
                    $check_entrenado_svg = '';
                    $check_entrenado = '';
                }

                $output .= '<div class="tr d-flex">';
                $output .= '<div class="td td-volunteer-check"><div class="td-padding"><input name="select_checked[]" class="select_checked" type="checkbox" value="' . $current_user . '"/></div></div>';
                $output .= '<div class="td td-20"><div class="td-padding td-padding-name">'.$name.'</div></div>';
                $output .= '<div class="td td-10"><div class="td-padding">'.$region.'</div></div>';
                $output .= '<div class="td td-25"><div class="td-padding">'.$mail.'</div></div>';
                $output .= '<div class="td td-10"><div class="td-padding">'.$language.'</div></div>';
                $output .= '<div class="td td-15"><div class="td-padding">'.$check_entrenado_svg.'</div></div>';
                $output .= '<div class="td td-15"><div class="td-padding">'.$check_status_svg.'</div></div>';
                $output .= '<div class="td td-100 volunteer-td-all-data"><div class="td td-100 d-flex flex-wrap">';
                $output .= '<div class="td td-20"><div class="td-padding"><span class="label">Other language:</span>'.$language_other.'</div></div>';
                $output .= '<div class="td td-10"><div class="td-padding"><span class="label">ID:</span>'.$id.'</div></div>';
                $output .= '<div class="td td-10"><div class="td-padding"><span class="label">Agreement:</span>'.$agreement.'</div></div>';
                $output .= '<div class="td td-10"><div class="td-padding"><span class="label">Certificate:</span>'.$certificate.'</div></div>';
                if(!empty($check_status)){
                    $output .= '<div class="td td-25"><div class="td-padding"><span class="label">'.pll__('Checked').': </span>'.$check_status.'</div></div>';
                }
                if(!empty($check_entrenado)){
                    $output .= '<div class="td td-25"><div class="td-padding"><span class="label">'.pll__('Entrenado').': </span>'.$check_entrenado.'</div></div>';
                }
                $output .= '</div></div>';
                $output .= '</div>';

            endforeach;
            echo json_encode([
                "html" => $output,
            ]);
        else:
            echo json_encode([
                "html" => pll__('Not found volunteers.'),
            ]);
        endif;
    endif;

    exit();
}

add_action('wp_ajax_cts_volunteers', 'cts_volunteers');
add_action('wp_ajax_nopriv_cts_volunteers', 'cts_volunteers');
function cts_volunteers() {
    check_ajax_referer( 'update_perfil', 'security' );
    if (isset($_POST['data']) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles())) :
        parse_str($_POST['data'], $form_data);
        $link = admin_url().'admin-ajax.php?action=export_update&ids=';
        foreach ($form_data['select_checked'] as $user){
            if(get_user_meta($user,'checked_user')[0]=='yes'){

            }else{
                update_user_meta($user,'checked_user', 'yes');
                update_user_meta($user,'user_checked_user', get_current_user_id());
                update_user_meta($user,'date_checked_user', date('Y-m-d'));
                $link.=$user.',';
            }
        }
        echo json_encode([
            "html" => pll__('Action completed. Click the following link to download the updated items.').'<br/><a href="'.$link.'" >'.pll__('Download').'</a>',
        ]);
    endif;
    exit();
}

add_action('wp_ajax_no_cts_volunteers', 'no_cts_volunteers');
add_action('wp_ajax_nopriv_no_cts_volunteers', 'no_cts_volunteers');
function no_cts_volunteers() {
    check_ajax_referer( 'update_perfil', 'security' );
    if (isset($_POST['data']) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles())) :
        parse_str($_POST['data'], $form_data);
        $link = admin_url().'admin-ajax.php?action=export_update&ids=';
        foreach ($form_data['select_checked'] as $user){
            if(get_user_meta($user,'checked_user')[0]=='yes'){
                update_user_meta($user,'checked_user', 'no');
                update_user_meta($user,'user_checked_user', '');
                update_user_meta($user,'date_checked_user', '');
                $link.=$user.',';
            }else{

            }
        }
        echo json_encode([
            "html" => pll__('Action completed. Click the following link to download the updated items.').'<br/><a href="'.$link.'" >'.pll__('Download').'</a>',
        ]);
    endif;
    exit();
}


add_action('wp_ajax_entrenado_volunteers', 'entrenado_volunteers');
add_action('wp_ajax_nopriv_entrenado_volunteers', 'entrenado_volunteers');
function entrenado_volunteers() {
    check_ajax_referer( 'update_perfil', 'security' );
    if (isset($_POST['data']) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles())) :
        parse_str($_POST['data'], $form_data);
        $link = admin_url().'admin-ajax.php?action=export_update&ids=';
        $tabla = '<table border="1"><tr><td>'.pll__('Name').'</td><td>'.pll__('Email').'</td><td>'.pll__('Date').'</td></tr>';
        foreach ($form_data['select_checked'] as $user){
            if(get_user_meta($user,'entrenado_user')[0]=='yes'){

            }else{
                update_user_meta($user,'entrenado_user', 'yes');
                update_user_meta($user,'user_entrenado_user', get_current_user_id());
                update_user_meta($user,'date_entrenado_user', date('Y-m-d'));
                $tabla .= '<tr><td>'.get_user_full_name($user).'</td><td>'.get_userdata($user)->user_email.'</td><td>'.date('d-m-Y').'</td></tr>';
                $link.=$user.',';
            }
        }
        $tabla .= '</table>';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $message_mail_user  = '<img width="300px" height="auto" src="'.get_template_directory_uri() . '/assets/images/logo-black.jpg"><br/><br/>';
        if(pll_current_language()=='en'):
            $content = get_post_field('post_content',8039);
        elseif(pll_current_language()=='hat'):
            $content = get_post_field('post_content',8040);
        else:
            $content = get_post_field('post_content',8037);
        endif;
        $message_mail_user .= str_replace('!tabla',$tabla,$content);
        wp_mail(get_bloginfo('admin_email'), pll__('Voluntarios entrenados'), $message_mail_user, $headers);
        $args = [
            'role' => 'entrenador',
        ];
        $entrenadores = get_users($args);
        foreach ($entrenadores as $entrenador) {
            wp_mail(get_userdata($entrenador->data->ID)->user_email, pll__('Voluntarios entrenados'), $message_mail_user, $headers);
        }
        echo json_encode([
            "html" => pll__('Action completed. Click the following link to download the updated items.').'<br/><a href="'.$link.'" >'.pll__('Download').'</a>',
        ]);
    endif;
    exit();
}

add_action('wp_ajax_no_entrenado_volunteers', 'no_entrenado_volunteers');
add_action('wp_ajax_nopriv_no_entrenado_volunteers', 'no_entrenado_volunteers');
function no_entrenado_volunteers() {
    check_ajax_referer( 'update_perfil', 'security' );
    if (isset($_POST['data']) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles())) :
        parse_str($_POST['data'], $form_data);
        $link = admin_url().'admin-ajax.php?action=export_update&ids=';
        $tabla = '<table border="1"><tr><td>'.pll__('Name').'</td><td>'.pll__('Email').'</td><td>'.pll__('Date').'</td></tr>';
        foreach ($form_data['select_checked'] as $user){
            if(get_user_meta($user,'entrenado_user')[0]=='yes'){
                update_user_meta($user,'entrenado_user', 'no');
                update_user_meta($user,'user_entrenado_user', '');
                update_user_meta($user,'date_entrenado_user', '');
                $tabla .= '<tr><td>'.get_user_full_name($user).'</td><td>'.get_userdata($user)->user_email.'</td><td>'.date('d-m-Y').'</td></tr>';
                $link.=$user.',';
            }else{

            }
        }
        $tabla .= '</table>';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $message_mail_user  = '<img width="300px" height="auto" src="'.get_template_directory_uri() . '/assets/images/logo-black.jpg"><br/><br/>';
        if(pll_current_language()=='en'):
            $content = get_post_field('post_content',7845);
        elseif(pll_current_language()=='hat'):
            $content = get_post_field('post_content',7846);
        else:
            $content = get_post_field('post_content',8043);
        endif;
        $message_mail_user .= str_replace('!tabla',$tabla,$content);
        wp_mail(get_bloginfo('admin_email'), pll__('Voluntarios no entrenados'), $message_mail_user, $headers);
        $args = [
            'role' => 'entrenador',
        ];
        $entrenadores = get_users($args);
        foreach ($entrenadores as $entrenador) {
            wp_mail(get_userdata($entrenador->data->ID)->user_email, pll__('Voluntarios no entrenados'), $message_mail_user, $headers);
        }
        echo json_encode([
            "html" => pll__('Action completed. Click the following link to download the updated items.').'<br/><a href="'.$link.'" >'.pll__('Download').'</a>',
        ]);
    endif;
    exit();
}

add_action('wp_ajax_export_update', 'export_update');
add_action('wp_ajax_nopriv_export_update', 'export_update');
function export_update() {
    if ( is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles()) ) {
        $authors = explode(',', $_GET['ids']);
        $xls_content = '';
        foreach ($authors as $request):
            if(!empty($request)){
                $user_id = $request;
                $user_name = get_user_meta($user_id, 'firstname_user')[0] . ' ' . get_user_meta($user_id, 'lastname_user')[0];
                $user_mail = get_userdata($user_id)->user_email;
                $region_user = get_user_meta($user_id, 'region_user')[0];
                $language_user = get_user_meta($user_id, 'language_user')[0];
                $languageother_user = get_user_meta($user_id, 'languageother_user')[0];
                $phone_user = get_user_meta($user_id, 'phone_user')[0];
                $city_user = get_user_meta($user_id, 'city_user')[0];
                $country_user = get_user_meta($user_id, 'country_user')[0];
                $province_user = get_user_meta($user_id, 'province_user')[0];
                $checked_user = get_user_meta($user_id, 'checked_user')[0];
                $user_checked_user = get_user_meta($user_id, 'user_checked_user')[0];
                if(!empty($user_checked_user)){
                    $user_checked_user = get_user_meta($user_checked_user, 'firstname_user')[0] . ' ' . get_user_meta($user_checked_user, 'lastname_user')[0];
                }else{
                    $user_checked_user = '';
                }
                $date_checked_user = get_user_meta($user_id, 'date_checked_user')[0];

                $xls_content .= '<tr>
                                 <td>' . utf8_decode($user_name) . '</td>
                                 <td>' . utf8_decode($user_mail) . '</td>
                                 <td>' . utf8_decode($region_user) . '</td>
                                 <td>' . utf8_decode($language_user) .'</td>
                                 <td>' . utf8_decode($languageother_user) . '</td>
                                 <td>' . utf8_decode($phone_user) . '</td>
                                 <td>' . utf8_decode($city_user) . '</td>
                                 <td>' . utf8_decode($country_user) . '</td>
                                 <td>' . utf8_decode($province_user) . '</td>
                                 <td>' . utf8_decode($checked_user) . '</td>
                                 <td>' . utf8_decode($user_checked_user) . '</td>
                                 <td>' . utf8_decode($date_checked_user) . '</td>
                              </tr>';

            }
        endforeach;
        $xls_header = '<table border="1">';
        $xls_header .= '<tr>
                                    <th><strong>' . utf8_decode('Name') . '</strong></th>
                                    <th><strong>' . utf8_decode('Email') . '</strong></th>
                                    <th><strong>' . utf8_decode('Region') . '</strong></th>
                                    <th><strong>' . utf8_decode('Language') . '</strong></th>
                                    <th><strong>' . utf8_decode('Other language') . '</strong></th>
                                    <th><strong>' . utf8_decode('Phone') . '</strong></th>
                                    <th><strong>' . utf8_decode('City') . '</strong></th>
                                    <th><strong>' . utf8_decode('Country') . '</strong></th>
                                    <th><strong>' . utf8_decode('Province') . '</strong></th>
                                    <th><strong>' . utf8_decode('Checked') . '</strong></th>
                                    <th><strong>' . utf8_decode('User checked') . '</strong></th>
                                    <th><strong>' . utf8_decode('Date checked') . '</strong></th>
                                </tr>';
        $xls_end = '</table>';
        $xls_output = $xls_header . $xls_content . $xls_end;
        $file = rand();
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=" . $file . ".xls");
        echo $xls_output;

    }
    exit;
}

add_action('wp_ajax_export_volunteers', 'export_volunteers');
add_action('wp_ajax_nopriv_export_volunteers', 'export_volunteers');
function export_volunteers() {
    if ( is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles()) ) {
        $volunteers_search_text = $_GET['text'];
        $volunteers_search_region = $_GET['region'];
        $volunteers_search_status = $_GET['status'];
        $volunteers_search_entrenado = $_GET['entrenado'];
        $output = '';
        $xls_content = '';
        $args = [
            'order' => 'ASC',
            'orderby' => 'display_name',
            'number' => 999999,
            'offset' => 0,
        ];

        $args['meta_query'][] =
            [
                'key' => 'volunteer_user',
                'value' => 'yes',
                'compare' => '=',
            ];

        if (!empty($volunteers_search_region) && $volunteers_search_region!='null') {
            $args['meta_query'][] =
                [
                    'key' => 'region_user',
                    'value' => $volunteers_search_region,
                    'compare' => '=',
                ];
        }

        if (!empty($volunteers_search_status) && $volunteers_search_status!='null') {
            $args['meta_query'][] =
                [
                    'key' => 'checked_user',
                    'value' => $volunteers_search_status,
                    'compare' => '=',
                ];
        }

        if (!empty($volunteers_search_entrenado) && $volunteers_search_entrenado!='null') {
            $args['meta_query'][] =
                [
                    'key' => 'entrenado_user',
                    'value' => $volunteers_search_entrenado,
                    'compare' => '=',
                ];
        }

        if (!empty($volunteers_search_text)) {
            $args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key' => 'firstname_user',
                    'value' => $volunteers_search_text,
                    'compare' => 'LIKE',
                ],
                [
                    'key' => 'lastname_user',
                    'value' => $volunteers_search_text,
                    'compare' => 'LIKE',
                ],
            ];
        }

        $wp_user_query = new WP_User_Query($args);
        $authors = $wp_user_query->get_results();


        foreach ($authors as $request):
            $user_id = $request->data->ID;
            $user_name = get_user_meta($user_id, 'firstname_user')[0] . ' ' . get_user_meta($user_id, 'lastname_user')[0];
            $user_mail = get_userdata($user_id)->user_email;
            $region_user = get_user_meta($user_id, 'region_user')[0];
            $language_user = get_user_meta($user_id, 'language_user')[0];
            $languageother_user = get_user_meta($user_id, 'languageother_user')[0];
            $phone_user = get_user_meta($user_id, 'phone_user')[0];
            $city_user = get_user_meta($user_id, 'city_user')[0];
            $country_user = get_user_meta($user_id, 'country_user')[0];
            $province_user = get_user_meta($user_id, 'province_user')[0];
            $checked_user = get_user_meta($user_id, 'checked_user')[0];
            $user_checked_user = get_user_meta($user_id, 'user_checked_user')[0];
            if(!empty($user_checked_user)){
                $user_checked_user = get_user_meta($user_checked_user, 'firstname_user')[0] . ' ' . get_user_meta($user_checked_user, 'lastname_user')[0];
            }else{
                $user_checked_user = '';
            }
            $date_checked_user = get_user_meta($user_id, 'date_checked_user')[0];

            $xls_content .= '<tr>
                                 <td>' . utf8_decode($user_name) . '</td>
                                 <td>' . utf8_decode($user_mail) . '</td>
                                 <td>' . utf8_decode($region_user) . '</td>
                                 <td>' . utf8_decode($language_user) .'</td>
                                 <td>' . utf8_decode($languageother_user) . '</td>
                                 <td>' . utf8_decode($phone_user) . '</td>
                                 <td>' . utf8_decode($city_user) . '</td>
                                 <td>' . utf8_decode($country_user) . '</td>
                                 <td>' . utf8_decode($province_user) . '</td>
                                 <td>' . utf8_decode($checked_user) . '</td>
                                 <td>' . utf8_decode($user_checked_user) . '</td>
                                 <td>' . utf8_decode($date_checked_user) . '</td>
                              </tr>';

        endforeach;
        $xls_header = '<table border="1">';
        $xls_header .= '<tr>
                                    <th><strong>' . utf8_decode(pll__('Name')) . '</strong></th>
                                    <th><strong>' . utf8_decode(pll__('Email')) . '</strong></th>
                                    <th><strong>' . utf8_decode(pll__('Region')) . '</strong></th>
                                    <th><strong>' . utf8_decode(pll__('Language')) . '</strong></th>
                                    <th><strong>' . utf8_decode(pll__('Other language')) . '</strong></th>
                                    <th><strong>' . utf8_decode(pll__('Phone')) . '</strong></th>
                                    <th><strong>' . utf8_decode(pll__('City')) . '</strong></th>
                                    <th><strong>' . utf8_decode(pll__('Country')) . '</strong></th>
                                    <th><strong>' . utf8_decode(pll__('Province')) . '</strong></th>
                                    <th><strong>' . utf8_decode(pll__('Checked')) . '</strong></th>
                                    <th><strong>' . utf8_decode(pll__('User checked')) . '</strong></th>
                                    <th><strong>' . utf8_decode(pll__('Date checked')) . '</strong></th>
                                </tr>';
        $xls_end = '</table>';
        $xls_output = $xls_header . $xls_content . $xls_end;
        $file = rand();
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=" . $file . ".xls");
        echo $xls_output;

    }
    exit;
}

add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

function volunteer_get_user_item($course_item, $uid) {
    $array = array();
    global $wpdb;
    $table_name = $wpdb->prefix . "learnpress_user_items";
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE `item_id`= %s AND `user_id`= %s", array($course_item,$uid)));
    foreach ($results as $result):
        if(!empty($result->start_time)):
            $array = array('user_item_id'=>$result->user_item_id, 'start_time'=>$result->start_time,'item_type'=>$result->item_type, 'status'=>$result->status);
        endif;
    endforeach;
    return $array;
}

function volunteer_get_status_user_item($user_item_id) {
    $status = '';
    global $wpdb;
    $table_name = $wpdb->prefix . "learnpress_user_itemmeta";
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE `learnpress_user_item_id`= %s AND `meta_key`= 'grade'", array($user_item_id)));
    foreach ($results as $result):
        $status = $result->meta_value;
    endforeach;
    return $status;
}

function get_plazas($event){
    $plazas = sprintf(pll__('%s Plazas'), 0);
    $array = array('count_plazas' => 0, 'text_plazas'=>$plazas);
    if(empty(get_field('cantidad_de_plazas',$event)) || get_field('cantidad_de_plazas',$event)==0 ){

    }else{
        $array['count_plazas'] = get_field('cantidad_de_plazas',$event);
        if(get_field('cantidad_de_plazas',$event)==1){
            $plazas = sprintf(pll__('%s Plaza'), get_field('cantidad_de_plazas',$event));
        }else{
            $plazas = sprintf(pll__('%s Plazas'), get_field('cantidad_de_plazas',$event));
        }
        $array['text_plazas'] = $plazas;
    }
    return $array;
}


add_action('wp_ajax_get_load_plazas', 'get_load_plazas');
add_action('wp_ajax_nopriv_get_load_plazas', 'get_load_plazas');
function get_load_plazas(){
    check_ajax_referer( 'update_perfil', 'security' );
    if (isset($_POST['data'])) :
        if(empty(get_field('cantidad_de_plazas',$_POST['data'])) || get_field('cantidad_de_plazas',$_POST['data'])==0 ){
            $plazas = sprintf(pll__('%s Plazas'), 0);
        }else{
            if(get_field('cantidad_de_plazas',$_POST['data'])==1){
                $plazas = sprintf(pll__('%s Plaza'), get_field('cantidad_de_plazas',$_POST['data']));
            }else{
                $plazas = sprintf(pll__('%s Plazas'), get_field('cantidad_de_plazas',$_POST['data']));
            }
        }

        echo json_encode([
            "html" => $plazas,
        ]);
    else:
        echo json_encode([
            "html" => sprintf(pll__('%s Plazas'), 0),
        ]);
    endif;
    exit();
}

function get_events_volunteer(){
    $html = '';
    $query    = new WP_Query(
        [
            'post_type' => 'mec-events',
            'post_status' => ['publish'],
            'posts_per_page' => 999,
            'meta_query' => [
                'relation' => 'AND',
                'mec_start_date' => [
                    'key' => 'mec_start_date',
                    'value' => date('Y-m-d'),
                    'type' => 'DATE',
                    'compare' => '>=',
                ]
            ],
            'orderby' => [
                'mec_start_date' => 'DESC',
            ],

        ]
    );
    $cantidad = $query->post_count;
    $events = $query->posts;
    foreach ($events as $event){
        $id = $event->ID;
        $cantidad_plazas = get_field('cantidad_de_plazas', $id);
        $event_hora = get_field('hora', $id);
        $zoom_link = get_field('zoom_link', $id);
        $event_entrenador = get_field('event_entrenador', $id);
        $mec_start_date = get_field('mec_start_date', $id);
        $title = get_the_title($id);
        $html .= '<div class="tr d-flex"><div class="td td-25"><div class="td-padding">'.pll__($title).' ('.$event_entrenador.')</div></div><div class="td td-15"><div class="td-padding">'.$mec_start_date.' ('.$event_hora.')</div></div><div class="td td-15"><div class="td-padding">'.get_plazas($id)['text_plazas'].'</div></div><div class="td td-15"><div class="td-padding"><a href="#" class="solicitudes-link" target-id="'.$id.'">'.get_request_by_event($id).'</a></div></div><div class="td td-15"><div class="td-padding"><a target="_blank" rel="noopener norefer" href="'.$zoom_link.'">'.pll__('Zoom link').'</a></div></div><div class="td td-15"><div class="td-padding"><a class="edit-link" target-id="'.$id.'" href="#">'.pll__('Edit').'</a> <a class="delete-link" target-id="'.$id.'" href="#">'.pll__('Delete').'</a></div></div><div class="td td-100 volunteer-td-all-data"><div class="td td-100 d-flex">
        <form id="edit-event-form-'.$id.'" class="edit-event-form d-flex flex-wrap">
                                        <div class="form-group">
                                            <select name="event_type" class="event_type form-select"><option value="">'.pll__('Select type').' *</option>';
        if(get_the_title($id)=='Entrenamiento'){
            $html .= '<option selected="selected" value="Entrenamiento">'.pll__('Entrenamiento').'</option>';
        }else{
            $html .= '<option value="Entrenamiento">'.pll__('Entrenamiento').'</option>';
        }
        if(get_the_title($id)=='Entrevista Final'){
            $html .= '<option selected="selected" value="Entrevista Final">'.pll__('Entrevista Final').'</option>';
        }else{
            $html .= '<option value="Entrevista Final">'.pll__('Entrevista Final').'</option>';
        }
        if(get_the_title($id)=='Video de Orientación'){
            $html .= '<option selected="selected" value="Video de Orientación">'.pll__('Video de Orientación').'</option>';
        }else{
            $html .= '<option value="Video de Orientación">'.pll__('Video de Orientación').'</option>';
        }
        $html .= '</select>
                                        </div>
                                        <input type="hidden" name="event_id" value="'.$id.'">
                                        <div class="form-group">
                                            <input value="'.$cantidad_plazas.'" type="text" name="event_plazas" class="form-input event_plazas form-input-small" placeholder="'.pll__('Cantidad de Plazas').' *">
                                        </div>
                                        <div class="form-group">
                                            <input value="'.$zoom_link.'"  type="text" name="zoom_link" class="form-input zoom_link form-input-small" placeholder="'.pll__('Zoom link').' *">
                                        </div>
                                        <div class="form-group">
                                            <input value="'.$event_hora.'"  type="text" name="hora" class="form-input zoom_link form-input-small" placeholder="'.pll__('Hour').' *">
                                        </div>
                                        <div class="form-group">
                                            <input value="'.$event_entrenador.'"  type="text" name="event_entrenador" class="form-input zoom_link form-input-small" placeholder="'.pll__('Coach').' *">
                                        </div>                          
                                        <div class="form-group">
                                            <input value="'.$mec_start_date.'"  type="date" name="event_date" class="form-input event_date form-input-small" placeholder="'.pll__('Date').' *"  />
                                        </div>
                                        <div class="form-group d-flex align-items-center">
                                            <button class="btn btn-primary btn-small" type="submit">'.pll__('Update').'</button>
                                        </div>
                                    </form>

        </div></div><div class="td td-100 volunteer-td-all-requests">';
        $query_request    = new WP_Query( array(
            'post_type'      => 'request',
            'posts_per_page' => 999,
            'post_status'    => array('publish','pending'),
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'     => 'place_request_event',
                    'value'   => $id,
                    'compare' => '='
                )
            )
        ) );
        $request_result = $query_request->posts;
        foreach ($request_result as $request_element){
            $requesid = $request_element->ID;
            $user_id = get_field('place_request_volunteer',$requesid);
            $user_name = get_user_meta($user_id, 'firstname_user')[0] . ' ' . get_user_meta($user_id, 'lastname_user')[0];
            $html .= '<div class="td td-100 d-flex"><form id="edit-request-form-'.$requesid.'" class="td-100 d-flex align-items-center edit-request-form d-flex flex-wrap">
                                        <div class="form-group td-30"><div class="td-padding">'.$user_name.'</div></div>
                                        <div class="form-group td-35"><div class="td-padding">
                                            <select name="event_id" class="event_type form-select">'.get_options_events($id). '</select>
                                        </div></div>
                                        <input type="hidden" name="request_id" value="'.$requesid.'">
                                        <input type="hidden" name="user_id" value="'.$user_id.'">
                                        <div class="form-group d-flex align-items-center">
                                            <button class="btn btn-primary btn-small" type="submit">'.pll__('Update').'</button>
                                        </div><div class="form-group flex-1"><div class="td-padding"><a class="delete-requests" target-id="'.$requesid.'" href="#">'.pll__('Delete').'</a></div></div>
                                    </form></div>';
        }
        $html .= '</div></div>';
    }
    return $html;
}

function get_request_by_event($event_id){
    $html = sprintf(pll__('%s solicitudes'), 0);
    $query    = new WP_Query( array(
        'post_type'      => 'request',
        'posts_per_page' => 999,
        'post_status'    => array('publish','pending'),
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key'     => 'place_request_event',
                'value'   => $event_id,
                'compare' => '='
            )
        )
    ) );
    $cantidad = $query->post_count;
    if($cantidad==1){
        $html = sprintf(pll__('%s solicitud'), $cantidad);
    }elseif($cantidad!=0){
        $html = sprintf(pll__('%s solicitudes'), $cantidad);
    }
    return $html;
}

add_action('wp_ajax_export_events', 'export_events');
add_action('wp_ajax_nopriv_export_events', 'export_events');
function export_events() {
    if ((isset($_GET['startdate']) || isset($_GET['enddate'])) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('entrenador', get_current_user_roles())) ) {

        $xls_content = '<table border="1">';
        $xls_content .= '<tr>
                                   <th><strong>' . utf8_decode('Event') . '</strong></th>
                                   <th><strong>' . utf8_decode('Date/Time') . '</strong></th>
                                    <th><strong>' . utf8_decode('Name') . '</strong></th>
                                    <th><strong>' . utf8_decode('Email') . '</strong></th>
                                    <th><strong>' . utf8_decode('Event') . '</strong></th>
                                    <th><strong>' . utf8_decode('Coach') . '</strong></th>
                                    <th><strong>' . utf8_decode('Zoom link') . '</strong></th>
                                </tr>';
        if(!empty($_GET['startdate']) && !empty($_GET['enddate'])){
            $query_events = new WP_Query([
                'post_type' => 'mec-events',
                'post_status' => ['publish'],
                'posts_per_page' => 999,
                'meta_query' => [
                    'relation' => 'AND',
                    'mec_start_date' => [
                        'key' => 'mec_start_date',
                        'value' => [$_GET['startdate'],$_GET['enddate']],
                        'type' => 'DATE',
                        'compare' => 'BETWEEN',
                    ],
                ],
                'orderby' => [
                    'mec_start_date' => 'ASC',
                ],

            ]);
        }
        elseif(!empty($_GET['startdate']) && empty($_GET['enddate'])){
            $query_events = new WP_Query([
                'post_type' => 'mec-events',
                'post_status' => ['publish'],
                'posts_per_page' => 999,
                'meta_query' => [
                    'relation' => 'AND',
                    'mec_start_date' => [
                        'key' => 'mec_start_date',
                        'value' => $_GET['startdate'],
                        'type' => 'DATE',
                        'compare' => '>=',
                    ]
                ],
                'orderby' => [
                    'mec_start_date' => 'ASC',
                ],

            ]);
        }else{
            $query_events = new WP_Query([
                'post_type' => 'mec-events',
                'post_status' => ['publish'],
                'posts_per_page' => 999,
                'meta_query' => [
                    'relation' => 'AND',
                    'mec_start_date' => [
                        'key' => 'mec_start_date',
                        'value' => $_GET['enddate'],
                        'type' => 'DATE',
                        'compare' => '<=',
                    ],
                ],
                'orderby' => [
                    'mec_start_date' => 'ASC',
                ],

            ]);
        }
        $results_events = $query_events->posts;
        foreach ($results_events as $event) {
            $event_id = $event->ID;
            $event_date = get_field('mec_start_date', $event_id);
            $event_zoom_link = get_field('zoom_link', $event_id);
            $event_title = pll__(get_the_title($event_id));
            $event_hora = get_field('hora', $event_id);
            $event_entrenador = get_field('event_entrenador', $event_id);
            $query = new WP_Query(array(
                'post_type' => 'request',
                'posts_per_page' => 999,
                'post_status' => array('publish', 'pending'),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'place_request_event',
                        'value' => $event_id,
                        'compare' => '='
                    )
                )
            ));
            $requests = $query->posts;

            foreach ($requests as $request) {
                $user_id = get_field('place_request_volunteer', $request->ID);
                $user_name = get_user_meta($user_id, 'firstname_user')[0] . ' ' . get_user_meta($user_id, 'lastname_user')[0];
                $user_mail = get_userdata($user_id)->user_email;
                $xls_content .= '<tr>
                                <td>' . utf8_decode($event_title) . '</td>
                                <td>' . utf8_decode($event_date) . ' (' . utf8_decode($event_hora) . ')</td>
                                <td>' . utf8_decode($user_name) . '</td>
                                 <td>' . utf8_decode($user_mail) . '</td>
                                 <td>' . utf8_decode($event_title) . '</td>
                                 <td>' . utf8_decode($event_entrenador) . '</td>
                                 <td>' . utf8_decode($event_zoom_link) . '</td>
                              </tr>';
            }
        }
        $xls_content .= '</table>';
        $xls_output = $xls_content;
        $file = rand();
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=" . $file . ".xls");
        echo $xls_output;
    }
    exit;
}

add_action('wp_ajax_volunteer_courses_statistics', 'volunteer_courses_statistics');
add_action('wp_ajax_nopriv_volunteer_courses_statistics', 'volunteer_courses_statistics');
function volunteer_courses_statistics() {
    check_ajax_referer( 'update_perfil', 'security' );
    $output = '<div class="tr d-flex"><div class="td td-55"><strong>'.pll__('Course').'</strong></div><div class="td td-15"><strong>'.pll__('Language').'</strong></div><div class="td td-15"><strong>'.pll__('Matriculados').'</strong></div><div class="td td-15"><strong>'.pll__('Graduados').'</strong></div></div>';
    $query    = new WP_Query(
        [
            'post_type' => 'lp_course',
            'post_status' => ['publish'],
            'posts_per_page' => 999,
            'lang' => ['es','en','hat'],
        ]
    );
    $events = $query->posts;
    foreach ($events as $event){
        $courseid= $event->ID;
        $titulo = get_the_title($courseid);
        $language = pll_get_post_language($courseid);
        if($language=='en'){
            $language= 'English';
        }elseif($language=='es'){
            $language= 'Español';
        }elseif($language=='hat'){
            $language= 'Kreole';
        }

        $args = [
            'order' => 'ASC',
            'number' => 99999,
            'offset' => 0,
        ];
        $args['meta_query'][] =
            [
                'key' => 'matriculado',
                'value' => 'yes',
                'compare' => '=',
            ];
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseid,
                    'compare' => '=',
                ];
        $wp_user_query = new WP_User_Query($args);
        $authors = $wp_user_query->get_results();
        $count_studens = count($authors);
        $args['meta_query'][] =
            [
                'key' => 'matricula_aprobado',
                'value' => 'yes',
                'compare' => '=',
            ];
        $wp_user_query = new WP_User_Query($args);
        $authors = $wp_user_query->get_results();
        $count_passed_studens = count($authors);
      $output .= '<div class="tr d-flex"><div class="td td-55">'.$titulo.'</div><div class="td td-15">'.$language.'</div><div class="td td-15">'.$count_studens.'</div><div class="td td-15">'.$count_passed_studens.'</div></div>';
    }
    echo json_encode([
        "html" => $output,
    ]);
    exit();
}


add_action('wp_ajax_export_estadistica', 'export_estadistica');
add_action('wp_ajax_nopriv_export_estadistica', 'export_estadistica');
function export_estadistica() {
    if ( is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles()) ) {
        $start = $_GET['startdate'];
        $end = $_GET['enddate'];
        $xls_content = '';
        $query    = new WP_Query(
            [
                'post_type' => 'lp_course',
                'post_status' => ['publish'],
                'posts_per_page' => 999,
                'lang' => ['es','en','hat'],
            ]
        );
        $events = $query->posts;
        foreach ($events as $event){
            $courseid= $event->ID;
            $titulo = get_the_title($courseid);
            $language = pll_get_post_language($courseid);
            if($language=='en'){
                $language= 'English';
            }elseif($language=='es'){
                $language= 'Español';
            }elseif($language=='hat'){
                $language= 'Kreole';
            }
            $args = [
                'order' => 'ASC',
                'number' => 99999,
                'offset' => 0,
            ];
            $args['meta_query'][] =
                [
                    'key' => 'matriculado',
                    'value' => 'yes',
                    'compare' => '=',
                ];
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseid,
                    'compare' => '=',
                ];
            if(!empty($start)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha',
                    'value' => $start,
                    'type' => 'date',
                    'compare' => '>=',
                ];
            }
            if(!empty($end)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha',
                    'value' => $end,
                    'type' => 'date',
                    'compare' => '<=',
                ];
            }
            $wp_user_query = new WP_User_Query($args);
            $authors = $wp_user_query->get_results();
            $count_studens = count($authors);
            $args = [
                'order' => 'ASC',
                'number' => 99999,
                'offset' => 0,
            ];
            $args['meta_query'][] =
                [
                    'key' => 'matriculado',
                    'value' => 'yes',
                    'compare' => '=',
                ];
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseid,
                    'compare' => '=',
                ];
            $args['meta_query'][] =
                [
                    'key' => 'matricula_aprobado',
                    'value' => 'yes',
                    'compare' => '=',
                ];
            if(!empty($start)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha_aprobado',
                    'value' => $start,
                    'type' => 'date',
                    'compare' => '>=',
                ];
            }
            if(!empty($end)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha_aprobado',
                    'value' => $end,
                    'type' => 'date',
                    'compare' => '<=',
                ];
            }
            $wp_user_query = new WP_User_Query($args);
            $authors = $wp_user_query->get_results();
            $count_passed_studens = count($authors);

            $xls_content .= '<tr><td><strong>'.utf8_decode($titulo).'</strong></td><td><strong>'.utf8_decode($language).'</strong></td><td><strong>'.utf8_decode($count_studens).'</strong></td><td><strong>'.utf8_decode($count_passed_studens).'</strong></td></tr>';
        }
        $text_Course = pll__('Course');
        $text_Language = pll__('Language');
        $text_Matriculados = pll__('Matriculados');
        $text_Graduados = pll__('Graduados');
        $xls_header = '<table border="1">';
        $xls_header .= '<tr>
                                    <th><strong>' . utf8_decode($text_Course) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_Language) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_Matriculados) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_Graduados) . '</strong></th>
                                </tr>';
        $xls_end = '</table>';
        $xls_output = $xls_header . $xls_content . $xls_end;
        $file = rand();
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=" . $file . ".xls");
        echo $xls_output;

    }
    exit;
}

add_action('wp_ajax_load_estadistica', 'load_estadistica');
add_action('wp_ajax_nopriv_load_estadistica', 'load_estadistica');
function load_estadistica() {
    check_ajax_referer( 'update_perfil', 'security' );
    if (isset($_POST['data']) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles())) :
        parse_str($_POST['data'], $form_data);


        $start = $form_data['start_event_date_entrenador'];
        $end = $form_data['end_event_date_entrenador'];

        $output = '<div class="tr d-flex"><div class="td td-55">'.pll__('Course').'</div><div class="td td-15">'.pll__('Language').'</div><div class="td td-15">'.pll__('Matriculados').'</div><div class="td td-15">'.pll__('Graduados').'</div></div>';
        $query    = new WP_Query(
            [
                'post_type' => 'lp_course',
                'post_status' => ['publish'],
                'posts_per_page' => 999,
                'lang' => ['es','en','hat'],
            ]
        );
        $events = $query->posts;
        foreach ($events as $event){
            $courseid= $event->ID;
            $titulo = get_the_title($courseid);
            $language = pll_get_post_language($courseid);
            if($language=='en'){
                $language= 'English';
            }elseif($language=='es'){
                $language= 'Español';
            }elseif($language=='hat'){
                $language= 'Kreole';
            }
            $args = [
                'order' => 'ASC',
                'number' => 99999,
                'offset' => 0,
            ];
            $args['meta_query'][] =
                [
                    'key' => 'matriculado',
                    'value' => 'yes',
                    'compare' => '=',
                ];
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseid,
                    'compare' => '=',
                ];
            if(!empty($start)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha',
                    'value' => $start,
                    'type' => 'date',
                    'compare' => '>=',
                ];
            }
            if(!empty($end)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha',
                    'value' => $end,
                    'type' => 'date',
                    'compare' => '<=',
                ];
            }
            $wp_user_query = new WP_User_Query($args);
            $authors = $wp_user_query->get_results();
            $count_studens = count($authors);
            $args = [
                'order' => 'ASC',
                'number' => 99999,
                'offset' => 0,
            ];
            $args['meta_query'][] =
                [
                    'key' => 'matriculado',
                    'value' => 'yes',
                    'compare' => '=',
                ];
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseid,
                    'compare' => '=',
                ];
            $args['meta_query'][] =
                [
                    'key' => 'matricula_aprobado',
                    'value' => 'yes',
                    'compare' => '=',
                ];
            if(!empty($start)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha_aprobado',
                    'value' => $start,
                    'type' => 'date',
                    'compare' => '>=',
                ];
            }
            if(!empty($end)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha_aprobado',
                    'value' => $end,
                    'type' => 'date',
                    'compare' => '<=',
                ];
            }
            $wp_user_query = new WP_User_Query($args);
            $authors = $wp_user_query->get_results();
            $count_passed_studens = count($authors);

            $output .= '<div class="tr d-flex"><div class="td td-55">'.$titulo.'</div><div class="td td-15">'.$language.'</div><div class="td td-15">'.$count_studens.'</div><div class="td td-15">'.$count_passed_studens.'</div></div>';
        }
        

            echo json_encode([
                "html" => $output,
            ]);
    endif;

    exit();
}

add_action('wp_ajax_volunteer_courses_statistics_paises', 'volunteer_courses_statistics_paises');
add_action('wp_ajax_nopriv_volunteer_courses_statistics_paises', 'volunteer_courses_statistics_paises');
function volunteer_courses_statistics_paises() {
    check_ajax_referer( 'update_perfil', 'security' );
    $query    = new WP_Query(
        [
            'post_type' => 'lp_course',
            'post_status' => ['publish'],
            'posts_per_page' => 999,
            'lang' => ['es','en','hat'],
        ]
    );
    $events = $query->posts;
    $count_studens = 0;
    $count_passed_studens = 0;
    $paises = array();
    foreach ($events as $event){
        $courseid= $event->ID;
        $titulo = get_the_title($courseid);
        $language = pll_get_post_language($courseid);
        if($language=='en'){
            $language= 'English';
        }elseif($language=='es'){
            $language= 'Español';
        }elseif($language=='hat'){
            $language= 'Kreole';
        }
        $args = [
            'order' => 'ASC',
            'number' => 99999,
            'offset' => 0,
        ];
        $args['meta_query'][] =
            [
                'key' => 'matriculado',
                'value' => 'yes',
                'compare' => '=',
            ];
        $args['meta_query'][] =
            [
                'key' => 'matricula_curso',
                'value' => $courseid,
                'compare' => '=',
            ];
        $wp_user_query = new WP_User_Query($args);
        $authors = $wp_user_query->get_results();
            foreach ($authors as $result):

                $aprobado = get_user_meta($result->ID,'matricula_aprobado', true);
                $paisid = get_user_meta($result->ID,'country_user_new', true);
                if(array_key_exists($paisid,$paises)){
                    $new_count = $paises[$paisid]['matriculados'] + 1;
                    $paises[$paisid]['matriculados'] = $new_count;
                }else{
                    $paises[$paisid] = array('matriculados' => 1, 'graduados' => 0);
                }

                if($aprobado=='yes'){
                        $new_count = $paises[$paisid]['graduados'] + 1;
                        $paises[$paisid]['graduados'] = $new_count;
                        $count_passed_studens++;
                    }

            endforeach;
    }

    $paisescount = 0;
    $matriculados = 0;
    $aprobados = 0;
    $output_body = '';
    ksort($paises);

    foreach ($paises as $key1 => $pais){
        $paisescount++;
        $matriculados+=$pais['matriculados'];
        $aprobados+=$pais['graduados'];
        if($key1=='zz'){
            $country_name = pll__('Undefined');
        }elseif(empty(get_country_name_by_code($key1))){
            $country_name = $key1;
        }else{
            $country_name = get_country_name_by_code($key1);
        }
        $rendimiento = 100*$pais['graduados']/$pais['matriculados'];
        $link = 'https://rastreadorescovid.larkinhospital.com/estadisticas-paises-detalle?startdate=&enddate=&curso=&pais='.$key1;
        $output_body .= '<div class="tr d-flex"><div class="td td-20">'.$country_name.'</div><div class="td td-20">'.$pais['matriculados'].'</div><div class="td td-20">'.$pais['graduados'].'</div><div class="td td-20">'.round($rendimiento, 2).'%</div><div class="td td-20"><a target="_blank" href="'.$link.'">'.pll__('Details').'</a></div></div>';
    }
    $rendimiento = 100*$aprobados/$matriculados;
    $texto_rendimiento = str_replace('!percent',round($rendimiento, 2), pll__('!percent% de rendimiento'));
    $output = '<div class="tr d-flex"><div class="td td-20"><strong>'.$paisescount.' '.pll__('Countries').'</strong></div><div class="td td-20"><strong>'.$matriculados.' '.pll__('Matriculados').'</strong></div><div class="td td-20"><strong>'.$aprobados.' '.pll__('Graduados').'</strong></div><div class="td td-20"><strong>'.$texto_rendimiento.'</strong></div><div class="td td-20"><strong>'.pll__('Details').'</strong></div></div>';
    $output.=$output_body;
    echo json_encode([
        "html" => $output,
    ]);
    exit();
}


add_action('wp_ajax_export_estadistica_paises', 'export_estadistica_paises');
add_action('wp_ajax_nopriv_export_estadistica_paises', 'export_estadistica_paises');
function export_estadistica_paises() {
    if ( is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles()) ) {
        $start = $_GET['startdate'];
        $end = $_GET['enddate'];
        $courseide = $_GET['courseide'];
        $paiside = $_GET['paiside'];
        $xls_content = '';

        if($courseide==0){
            $query    = new WP_Query(
                [
                    'post_type' => 'lp_course',
                    'post_status' => ['publish'],
                    'posts_per_page' => 999,
                    'lang' => ['es','en','hat'],
                ]
            );
        }else{
            $query    = new WP_Query(
                [
                    'post_type' => 'lp_course',
                    'post_status' => ['publish'],
                    'posts_per_page' => 999,
                    'lang' => ['es','en','hat'],
                    'post__in' => array($courseide),
                ]
            );
        }

        $events = $query->posts;
        $count_studens = 0;
        $count_passed_studens = 0;
        $paises = array();
        global $wpdb;
        $table_name = $wpdb->prefix . "learnpress_user_items";
        foreach ($events as $event){
            $courseid= $event->ID;
            $titulo = get_the_title($courseid);
            $language = pll_get_post_language($courseid);
            if($language=='en'){
                $language= 'English';
            }elseif($language=='es'){
                $language= 'Español';
            }elseif($language=='hat'){
                $language= 'Kreole';
            }

            $args = [
                'order' => 'ASC',
                'number' => 99999,
                'offset' => 0,
            ];
            $args['meta_query'][] =
                [
                    'key' => 'matriculado',
                    'value' => 'yes',
                    'compare' => '=',
                ];
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseid,
                    'compare' => '=',
                ];
            if(!empty($start)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha',
                    'value' => $start,
                    'type' => 'date',
                    'compare' => '>=',
                ];
            }
            if(!empty($end)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha',
                    'value' => $end,
                    'type' => 'date',
                    'compare' => '<=',
                ];
            }
            if($paiside!='0'){
                $args['meta_query'][] =
                    [
                        'key' => 'country_user_new',
                        'value' => $paiside,
                        'compare' => '=',
                    ];
            }
            $wp_user_query = new WP_User_Query($args);
            $authors = $wp_user_query->get_results();
            foreach ($authors as $author){
                $paisid = get_user_meta($author->ID, 'country_user_new', true);
                if (array_key_exists($paisid, $paises)) {
                    $new_count = $paises[$paisid]['matriculados'] + 1;
                    $paises[$paisid]['matriculados'] = $new_count;
                } else {
                    $paises[$paisid] = array('matriculados' => 1, 'graduados' => 0);
                }
            }
            $args = [
                'order' => 'ASC',
                'number' => 99999,
                'offset' => 0,
            ];
            $args['meta_query'][] =
                [
                    'key' => 'matriculado',
                    'value' => 'yes',
                    'compare' => '=',
                ];
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseid,
                    'compare' => '=',
                ];
            $args['meta_query'][] =
                [
                    'key' => 'matricula_aprobado',
                    'value' => 'yes',
                    'compare' => '=',
                ];
            if(!empty($start)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha_aprobado',
                    'value' => $start,
                    'type' => 'date',
                    'compare' => '>=',
                ];
            }
            if(!empty($end)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha_aprobado',
                    'value' => $end,
                    'type' => 'date',
                    'compare' => '<=',
                ];
            }
            if($paiside!='0'){
                $args['meta_query'][] =
                    [
                        'key' => 'country_user_new',
                        'value' => $paiside,
                        'compare' => '=',
                    ];
            }
            $wp_user_query = new WP_User_Query($args);
            $authors = $wp_user_query->get_results();
            foreach ($authors as $author){
                $paisid = get_user_meta($author->ID, 'country_user_new', true);
                $new_count = $paises[$paisid]['graduados'] + 1;
                $paises[$paisid]['graduados'] = $new_count;
            }

        }

        $paisescount = 0;
        $matriculados = 0;
        $aprobados = 0;
        $output_body = '';
        ksort($paises);
        foreach ($paises as $key1 => $pais){
            $paisescount++;
            $matriculados+=$pais['matriculados'];
            $aprobados+=$pais['graduados'];
            if($key1=='zz'){
                $country_name = pll__('Undefined');
            }elseif(empty(get_country_name_by_code($key1))){
                $country_name = $key1;
            }else{
                $country_name = get_country_name_by_code($key1);
            }
            $rendimiento = 100*$pais['graduados']/$pais['matriculados'];
            $xls_content .= '<tr><td>'.$country_name.'</td><td>'.$pais['matriculados'].'</td><td>'.$pais['graduados'].'</td><td>'.round($rendimiento, 2).'%</td></tr>';
        }

        $text_Course = $paisescount.' '.pll__('Countries');
        $text_Language = $matriculados.' '.pll__('Matriculados');
        $text_Matriculados = $aprobados.' '.pll__('Graduados');
        $rendimiento = 100*$aprobados/$matriculados;
        $texto_rendimiento = str_replace('!percent',round($rendimiento, 2), pll__('!percent% de rendimiento'));
        $xls_header = '<table border="1">';
        $xls_header .= '<tr>
                                    <th><strong>' . utf8_decode($text_Course) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_Language) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_Matriculados) . '</strong></th>
                                    <th><strong>' . utf8_decode($texto_rendimiento) . '</strong></th>
                                </tr>';
        $xls_end = '</table>';
        $xls_output = $xls_header . $xls_content . $xls_end;
        $file = rand();
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=" . $file . ".xls");
        echo $xls_output;

    }
    exit;
}

add_action('wp_ajax_load_estadistica_paises', 'load_estadistica_paises');
add_action('wp_ajax_nopriv_load_estadistica_paises', 'load_estadistica_paises');
function load_estadistica_paises() {
    check_ajax_referer( 'update_perfil', 'security' );
    if (isset($_POST['data']) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles())) :
        parse_str($_POST['data'], $form_data);


        $start = $form_data['start_event_date_entrenador'];
        $end = $form_data['end_event_date_entrenador'];
        $courseide = $form_data['curso_entrenador_select'];
        $paiside = $form_data['country_entrenador_select'];

        if($courseide==0){
            $query    = new WP_Query(
                [
                    'post_type' => 'lp_course',
                    'post_status' => ['publish'],
                    'posts_per_page' => 999,
                    'lang' => ['es','en','hat'],
                ]
            );
        }else{
            $query    = new WP_Query(
                [
                    'post_type' => 'lp_course',
                    'post_status' => ['publish'],
                    'posts_per_page' => 999,
                    'lang' => ['es','en','hat'],
                    'post__in' => array($courseide),
                ]
            );
        }
        $events = $query->posts;
        $paises = array();
        foreach ($events as $event){
            $courseid= $event->ID;
            $titulo = get_the_title($courseid);
            $language = pll_get_post_language($courseid);
            if($language=='en'){
                $language= 'English';
            }elseif($language=='es'){
                $language= 'Español';
            }elseif($language=='hat'){
                $language= 'Kreole';
            }

            $args = [
                'order' => 'ASC',
                'number' => 99999,
                'offset' => 0,
            ];
            $args['meta_query'][] =
                [
                    'key' => 'matriculado',
                    'value' => 'yes',
                    'compare' => '=',
                ];
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseid,
                    'compare' => '=',
                ];
            if(!empty($start)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha',
                    'value' => $start,
                    'type' => 'date',
                    'compare' => '>=',
                ];
            }
            if(!empty($end)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha',
                    'value' => $end,
                    'type' => 'date',
                    'compare' => '<=',
                ];
            }
            if($paiside!='0'){
                $args['meta_query'][] =
                    [
                        'key' => 'country_user_new',
                        'value' => $paiside,
                        'compare' => '=',
                    ];
            }
            $wp_user_query = new WP_User_Query($args);
            $authors = $wp_user_query->get_results();
            foreach ($authors as $author){
                $paisid = get_user_meta($author->ID, 'country_user_new', true);
                if (array_key_exists($paisid, $paises)) {
                    $new_count = $paises[$paisid]['matriculados'] + 1;
                    $paises[$paisid]['matriculados'] = $new_count;
                } else {
                    $paises[$paisid] = array('matriculados' => 1, 'graduados' => 0);
                }
            }
            $args = [
                'order' => 'ASC',
                'number' => 99999,
                'offset' => 0,
            ];
            $args['meta_query'][] =
                [
                    'key' => 'matriculado',
                    'value' => 'yes',
                    'compare' => '=',
                ];
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseid,
                    'compare' => '=',
                ];
            $args['meta_query'][] =
                [
                    'key' => 'matricula_aprobado',
                    'value' => 'yes',
                    'compare' => '=',
                ];
            if(!empty($start)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha_aprobado',
                    'value' => $start,
                    'type' => 'date',
                    'compare' => '>=',
                ];
            }
            if(!empty($end)) {
                $args['meta_query'][] = [
                    'key' => 'matricula_fecha_aprobado',
                    'value' => $end,
                    'type' => 'date',
                    'compare' => '<=',
                ];
            }
            if($paiside!='0'){
                $args['meta_query'][] =
                    [
                        'key' => 'country_user_new',
                        'value' => $paiside,
                        'compare' => '=',
                    ];
            }
            $wp_user_query = new WP_User_Query($args);
            $authors = $wp_user_query->get_results();
            foreach ($authors as $author){
                $paisid = get_user_meta($author->ID, 'country_user_new', true);
                    $new_count = $paises[$paisid]['graduados'] + 1;
                    $paises[$paisid]['graduados'] = $new_count;
            }
        }

        $paisescount = 0;
        $matriculados = 0;
        $aprobados = 0;
        $output_body = '';
        ksort($paises);

        foreach ($paises as $key1 => $pais){
            $paisescount++;
            $matriculados+=$pais['matriculados'];
            $aprobados+=$pais['graduados'];
            if($key1=='zz'){
                $country_name = pll__('Undefined');
            }elseif(empty(get_country_name_by_code($key1))){
                $country_name = $key1;
            }else{
                $country_name = get_country_name_by_code($key1);
            }
            $rendimiento = 100*$pais['graduados']/$pais['matriculados'];

            $link = 'https://rastreadorescovid.larkinhospital.com/estadisticas-paises-detalle?startdate='.$start.'&enddate='.$end.'&curso='.$courseide.'&pais='.$key1;
            $output_body .= '<div class="tr d-flex"><div class="td td-20">'.$country_name.'</div><div class="td td-20">'.$pais['matriculados'].'</div><div class="td td-20">'.$pais['graduados'].'</div><div class="td td-20">'.round($rendimiento, 2).'%</div><div class="td td-20"><a target="_blank" href="'.$link.'">'.pll__('Details').'</a></div></div>';
        }

        $rendimiento = 100*$aprobados/$matriculados;
        $texto_rendimiento = str_replace('!percent',round($rendimiento, 2), pll__('!percent% de rendimiento'));
        $output = '<div class="tr d-flex"><div class="td td-20"><strong>'.$paisescount.' '.pll__('Countries').'</strong></div><div class="td td-20"><strong>'.$matriculados.' '.pll__('Matriculados').'</strong></div><div class="td td-20"><strong>'.$aprobados.' '.pll__('Graduados').'</strong></div><div class="td td-20"><strong>'.$texto_rendimiento.'</strong></div><div class="td td-20"><strong>'.pll__('Details').'</strong></div></div>';
        $output.=$output_body;


        echo json_encode([
            "html" => $output,
        ]);
    endif;

    exit();
}

add_action('wp_ajax_volunteer_courses_statistics_paises_1', 'volunteer_courses_statistics_paises_1');
add_action('wp_ajax_nopriv_volunteer_courses_statistics_paises_1', 'volunteer_courses_statistics_paises_1');
function volunteer_courses_statistics_paises_1() {
    check_ajax_referer( 'update_perfil', 'security' );

    $args = [
        'order' => 'ASC',
        'number' => 99999,
        'offset' => 0,
    ];
    $args['meta_query'][] =
        [
            'key' => 'matriculado',
            'value' => 'yes',
            'compare' => '=',
        ];
    $args['meta_query'][] =
        [
            'key' => 'matricula_aprobado',
            'value' => 'yes',
            'compare' => '=',
        ];
    $wp_user_query = new WP_User_Query($args);
    $authors = $wp_user_query->get_results();
    $final_count=0;
    $output_body = '';
    foreach ($authors as $user){
        $final_count++;
        $nombre = get_user_full_name($user->ID);
        $email = get_userdata($user->ID)->user_email;
        $paisid = get_user_meta($user->ID, 'country_user_new', true);
        $country_name = get_country_name_by_code($paisid);
        $output_body .= '<div class="tr d-flex"><div class="td td-33">'.$nombre.'</div><div class="td td-33">'.$email.'</div><div class="td td-33">'.$country_name.'</div></div>';
    }

    if($final_count==0){
        $summary = pll__('No se han encontrado elementos.');
    }elseif($final_count==1){
        $summary = pll__('Se ha encontrado 1 elemento.');
    }else{
        $summary = pll__('Se han encontrado !num elementos.');
        $summary = str_replace('!num', '<strong>'.$final_count.'</strong>', $summary);
    }
    $output = '<div class="tr d-flex"><div class="td td-33"><strong>'.pll__('Name').'</strong></div><div class="td td-33"><strong>'.pll__('Email').'</strong></div><div class="td td-33"><strong>'.pll__('Country').'</strong></div></div>';
    $output.=$output_body;
    echo json_encode([
        "html" => $output,
        "summary" => $summary,
    ]);
    exit();
}


add_action('wp_ajax_export_estadistica_paises_1', 'export_estadistica_paises_1');
add_action('wp_ajax_nopriv_export_estadistica_paises_1', 'export_estadistica_paises_1');
function export_estadistica_paises_1() {
    if ( is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles()) ) {
        $start = $_GET['startdate'];
        $end = $_GET['enddate'];
        $courseide = $_GET['courseide'];
        $paiside = $_GET['paiside'];
        $xls_content = '';
        $output_body = '';

        $args = [
            'order' => 'ASC',
            'number' => 99999,
            'offset' => 0,
        ];


        $args['meta_query'][] =
            [
                'key' => 'matriculado',
                'value' => 'yes',
                'compare' => '=',
            ];
        $args['meta_query'][] =
            [
                'key' => 'matricula_aprobado',
                'value' => 'yes',
                'compare' => '=',
            ];
        if($courseide!=0){
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseide,
                    'compare' => '=',
                ];
        }
        if($paiside!='0'){
            $args['meta_query'][] =
                [
                    'key' => 'country_user_new',
                    'value' => $paiside,
                    'compare' => '=',
                ];
        }
        if(!empty($start)) {
            $args['meta_query'][] = [
                'key' => 'matricula_fecha_aprobado',
                'value' => $start,
                'type' => 'date',
                'compare' => '>=',
            ];
        }
        if(!empty($end)) {
            $args['meta_query'][] = [
                'key' => 'matricula_fecha_aprobado',
                'value' => $end,
                'type' => 'date',
                'compare' => '<=',
            ];
        }
        $wp_user_query = new WP_User_Query($args);
        $authors = $wp_user_query->get_results();
        foreach ($authors as $user){
            $nombre = utf8_decode(get_user_full_name($user->ID));
            $email = get_userdata($user->ID)->user_email;
            $paisid = get_user_meta($user->ID, 'country_user_new', true);
            $city = utf8_decode(get_user_meta($user->ID, 'city_user', true));
            $country_name = utf8_decode(get_country_name_by_code($paisid));
            $xls_content .= '<tr><td>'.$nombre.'</td><td>'.$email.'</td><td>'.$country_name.'</td><td>'.ucwords(strtolower($city)).'</td></tr>';
        }

        $text_Course = pll__('Name');
        $text_Language = pll__('Email');
        $text_Matriculados = pll__('Country');
        $text_ciy = pll__('City');
        $xls_header = '<table border="1">';
        $xls_header .= '<tr>
                                    <th><strong>' . utf8_decode($text_Course) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_Language) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_Matriculados) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_ciy) . '</strong></th>
                                </tr>';
        $xls_end = '</table>';
        $xls_output = $xls_header . $xls_content . $xls_end;
        $file = rand();
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=" . $file . ".xls");
        echo $xls_output;

    }
    exit;
}

add_action('wp_ajax_load_estadistica_paises_1', 'load_estadistica_paises_1');
add_action('wp_ajax_nopriv_load_estadistica_paises_1', 'load_estadistica_paises_1');
function load_estadistica_paises_1() {
    check_ajax_referer( 'update_perfil', 'security' );
    if (isset($_POST['data']) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles())) :
        parse_str($_POST['data'], $form_data);


        $start = $form_data['start_event_date_entrenador'];
        $end = $form_data['end_event_date_entrenador'];
        $courseide = $form_data['curso_entrenador_select'];
        $paisidee = $form_data['country_entrenador_select'];

        $args = [
            'order' => 'ASC',
            'number' => 99999,
            'offset' => 0,
        ];
        $args['meta_query'][] =
            [
                'key' => 'matriculado',
                'value' => 'yes',
                'compare' => '=',
            ];
        $args['meta_query'][] =
            [
                'key' => 'matricula_aprobado',
                'value' => 'yes',
                'compare' => '=',
            ];
        if(!empty($start)) {
            $args['meta_query'][] = [
                'key' => 'matricula_fecha_aprobado',
                'value' => $start,
                'type' => 'date',
                'compare' => '>=',
            ];
        }
        if(!empty($end)) {
            $args['meta_query'][] = [
                'key' => 'matricula_fecha_aprobado',
                'value' => $end,
                'type' => 'date',
                'compare' => '<=',
            ];
        }
        if($courseide!=0){
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseide,
                    'compare' => '=',
                ];
        }
        if($paisidee!='0'){
            $args['meta_query'][] =
                [
                    'key' => 'country_user_new',
                    'value' => $paisidee,
                    'compare' => '=',
                ];
        }
        $wp_user_query = new WP_User_Query($args);
        $authors = $wp_user_query->get_results();
        $final_count=0;
        $output_body = '';
        foreach ($authors as $user){
            $final_count++;
            $nombre = get_user_full_name($user->ID);
            $email = get_userdata($user->ID)->user_email;
            $paisid = get_user_meta($user->ID, 'country_user_new', true);
            $country_name = get_country_name_by_code($paisid);
            $output_body .= '<div class="tr d-flex"><div class="td td-33">'.$nombre.'</div><div class="td td-33">'.$email.'</div><div class="td td-33">'.$country_name.'</div></div>';
        }

        $output = '<div class="tr d-flex"><div class="td td-33"><strong>'.pll__('Name').'</strong></div><div class="td td-33"><strong>'.pll__('Email').'</strong></div><div class="td td-33"><strong>'.pll__('Country').'</strong></div></div>';
        $output.=$output_body;
        if($final_count==0){
            $summary = pll__('No se han encontrado elementos.');
        }elseif($final_count==1){
            $summary = pll__('Se ha encontrado 1 elemento.');
        }else{
            $summary = pll__('Se han encontrado !num elementos.');
            $summary = str_replace('!num', '<strong>'.$final_count.'</strong>', $summary);
        }

        echo json_encode([
            "html" => $output,
            "summary" => $summary,
        ]);
    endif;

    exit();
}

add_action('wp_ajax_volunteer_courses_statistics_paises_vol', 'volunteer_courses_statistics_paises_vol');
add_action('wp_ajax_nopriv_volunteer_courses_statistics_paises_vol', 'volunteer_courses_statistics_paises_vol');
function volunteer_courses_statistics_paises_vol() {
    check_ajax_referer( 'update_perfil', 'security' );

    $args = [
        'order' => 'ASC',
        'number' => 99999,
        'offset' => 0,
    ];
    $args['meta_query'][] =
        [
            'key' => 'matriculado',
            'value' => 'yes',
            'compare' => '=',
        ];
    $args['meta_query'][] =
        [
            'key' => 'matricula_aprobado',
            'value' => 'yes',
            'compare' => '=',
        ];
    $wp_user_query = new WP_User_Query($args);
    $authors = $wp_user_query->get_results();
    $final_count=0;
    $output_body = '';
    foreach ($authors as $user){
        $final_count++;
        $nombre = get_user_full_name($user->ID);
        $email = get_userdata($user->ID)->user_email;
        $paisid = get_user_meta($user->ID, 'country_user_new', true);
        $country_name = get_country_name_by_code($paisid);
        $output_body .= '<div class="tr d-flex"><div class="td td-33">'.$nombre.'</div><div class="td td-33">'.$email.'</div><div class="td td-33">'.$country_name.'</div></div>';
    }

    if($final_count==0){
        $summary = pll__('No se han encontrado elementos.');
    }elseif($final_count==1){
        $summary = pll__('Se ha encontrado 1 elemento.');
    }else{
        $summary = pll__('Se han encontrado !num elementos.');
        $summary = str_replace('!num', '<strong>'.$final_count.'</strong>', $summary);
    }
    $output = '<div class="tr d-flex"><div class="td td-33"><strong>'.pll__('Name').'</strong></div><div class="td td-33"><strong>'.pll__('Email').'</strong></div><div class="td td-33"><strong>'.pll__('Country').'</strong></div></div>';
    $output.=$output_body;
    echo json_encode([
        "html" => $output,
        "summary" => $summary,
    ]);
    exit();
}


add_action('wp_ajax_export_estadistica_paises_vol', 'export_estadistica_paises_vol');
add_action('wp_ajax_nopriv_export_estadistica_paises_vol', 'export_estadistica_paises_vol');
function export_estadistica_paises_vol() {
    if ( is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles()) ) {
        $start = $_GET['startdate'];
        $end = $_GET['enddate'];
        $courseide = $_GET['courseide'];
        $paiside = $_GET['paiside'];
        $voluntarios = $_GET['voluntario'];
        $chekeado = $_GET['chekeado'];
        $xls_content = '';
        $output_body = '';

        $args = [
            'order' => 'ASC',
            'number' => 99999,
            'offset' => 0,
        ];


        $args['meta_query'][] =
            [
                'key' => 'matriculado',
                'value' => 'yes',
                'compare' => '=',
            ];
        $args['meta_query'][] =
            [
                'key' => 'matricula_aprobado',
                'value' => 'yes',
                'compare' => '=',
            ];
        if($voluntarios!='0'){
            $args['meta_query'][] =
                [
                    'key' => 'volunteer_user',
                    'value' => $voluntarios,
                    'compare' => '=',
                ];
        }
        if($chekeado=='checked'){
            $args['meta_query'][] =
                [
                    'key' => 'checked_user',
                    'value' => 'yes',
                    'compare' => '=',
                ];
        }
        if($courseide!=0){
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseide,
                    'compare' => '=',
                ];
        }
        if($paiside!='0'){
            $args['meta_query'][] =
                [
                    'key' => 'country_user_new',
                    'value' => $paiside,
                    'compare' => '=',
                ];
        }
        if(!empty($start)) {
            $args['meta_query'][] = [
                'key' => 'matricula_fecha_aprobado',
                'value' => $start,
                'type' => 'date',
                'compare' => '>=',
            ];
        }
        if(!empty($end)) {
            $args['meta_query'][] = [
                'key' => 'matricula_fecha_aprobado',
                'value' => $end,
                'type' => 'date',
                'compare' => '<=',
            ];
        }
        $wp_user_query = new WP_User_Query($args);
        $authors = $wp_user_query->get_results();
        foreach ($authors as $user){
            $nombre = get_user_full_name($user->ID);
            $email = get_userdata($user->ID)->user_email;
            $paisid = get_user_meta($user->ID, 'country_user_new', true);
            $country_name = get_country_name_by_code($paisid);
            $xls_content .= '<tr><td>'.$nombre.'</td><td>'.$email.'</td><td>'.$country_name.'</td></tr>';
        }

        $text_Course = pll__('Name');
        $text_Language = pll__('Email');
        $text_Matriculados = pll__('Country');
        $xls_header = '<table border="1">';
        $xls_header .= '<tr>
                                    <th><strong>' . utf8_decode($text_Course) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_Language) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_Matriculados) . '</strong></th>
                                </tr>';
        $xls_end = '</table>';
        $xls_output = $xls_header . $xls_content . $xls_end;
        $file = rand();
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=" . $file . ".xls");
        echo $xls_output;

    }
    exit;
}

add_action('wp_ajax_load_estadistica_paises_vol', 'load_estadistica_paises_vol');
add_action('wp_ajax_nopriv_load_estadistica_paises_vol', 'load_estadistica_paises_vol');
function load_estadistica_paises_vol() {
    check_ajax_referer( 'update_perfil', 'security' );
    if (isset($_POST['data']) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles())) :
        parse_str($_POST['data'], $form_data);


        $start = $form_data['start_event_date_entrenador'];
        $end = $form_data['end_event_date_entrenador'];
        $courseide = $form_data['curso_entrenador_select'];
        $paisidee = $form_data['country_entrenador_select'];
        $voluntarios = $form_data['voluntario_entrenador_select'];
        $chekeado = $form_data['voluntario_entrenador_checked'];
        $args = [
            'order' => 'ASC',
            'number' => 99999,
            'offset' => 0,
        ];
        $args['meta_query'][] =
            [
                'key' => 'matriculado',
                'value' => 'yes',
                'compare' => '=',
            ];
        $args['meta_query'][] =
            [
                'key' => 'matricula_aprobado',
                'value' => 'yes',
                'compare' => '=',
            ];
        if(!empty($start)) {
            $args['meta_query'][] = [
                'key' => 'matricula_fecha_aprobado',
                'value' => $start,
                'type' => 'date',
                'compare' => '>=',
            ];
        }
        if(!empty($end)) {
            $args['meta_query'][] = [
                'key' => 'matricula_fecha_aprobado',
                'value' => $end,
                'type' => 'date',
                'compare' => '<=',
            ];
        }
        if($voluntarios!='0'){
            $args['meta_query'][] =
                [
                    'key' => 'volunteer_user',
                    'value' => $voluntarios,
                    'compare' => '=',
                ];
        }
        if($chekeado=='checked'){
            $args['meta_query'][] =
                [
                    'key' => 'checked_user',
                    'value' => 'yes',
                    'compare' => '=',
                ];
        }
        if($courseide!=0){
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseide,
                    'compare' => '=',
                ];
        }
        if($paisidee!='0'){
            $args['meta_query'][] =
                [
                    'key' => 'country_user_new',
                    'value' => $paisidee,
                    'compare' => '=',
                ];
        }
        $wp_user_query = new WP_User_Query($args);
        $authors = $wp_user_query->get_results();
        $final_count=0;
        $output_body = '';
        foreach ($authors as $user){
            $final_count++;
            $nombre = get_user_full_name($user->ID);
            $email = get_userdata($user->ID)->user_email;
            $paisid = get_user_meta($user->ID, 'country_user_new', true);
            $country_name = get_country_name_by_code($paisid);
            $output_body .= '<div class="tr d-flex"><div class="td td-33">'.$nombre.'</div><div class="td td-33">'.$email.'</div><div class="td td-33">'.$country_name.'</div></div>';
        }

        $output = '<div class="tr d-flex"><div class="td td-33"><strong>'.pll__('Name').'</strong></div><div class="td td-33"><strong>'.pll__('Email').'</strong></div><div class="td td-33"><strong>'.pll__('Country').'</strong></div></div>';
        $output.=$output_body;
        if($final_count==0){
            $summary = pll__('No se han encontrado elementos.');
        }elseif($final_count==1){
            $summary = pll__('Se ha encontrado 1 elemento.');
        }else{
            $summary = pll__('Se han encontrado !num elementos.');
            $summary = str_replace('!num', '<strong>'.$final_count.'</strong>', $summary);
        }

        echo json_encode([
            "html" => $output,
            "summary" => $summary,
        ]);
    endif;

    exit();
}


function get_options_events($event_id){
    $html = '';

    $query    = new WP_Query(
        [
            'post_type' => 'mec-events',
            'post_status' => ['publish'],
            'posts_per_page' => 999,
            'meta_query' => [
                'mec_start_date' => [
                    'key' => 'mec_start_date',
                    'compare' => 'EXISTS',
                ],
            ],
            'orderby' => [
                'mec_start_date' => 'DESC',
            ],

        ]
    );
    $cantidad = $query->post_count;
    $events = $query->posts;

    foreach ($events as $event){
        $mec_start_date = get_field('mec_start_date', $event->ID);
        $hora = get_field('hora', $event->ID);
        if($event_id==$event->ID){
            $html .= '<option selected="selected" value="'.$event->ID.'">'.get_the_title($event->ID).' ('.$mec_start_date.' - '.$hora.')</option>';
        }else{
            $html .= '<option value="'.$event->ID.'">'.get_the_title($event->ID).' ('.$mec_start_date.' - '.$hora.')</option>';
        }
    }
    return $html;
}

add_action( 'wp_print_styles',     'my_deregister_styles', 100 );
function my_deregister_styles()    {

    if(!is_user_logged_in() || ((!current_user_can('administrator') && !is_admin()))){
        wp_deregister_style( 'dashicons' );
    }

    wp_deregister_style( 'bodhi-svgs-attachment' );
    wp_deregister_style( 'moove_gdpr_lity' );
    wp_deregister_style( 'cookie-bar-css' );

    if (!is_page_template('page-training.php')) {
        wp_deregister_style( 'mec-lity-style' );
        wp_deregister_style( 'mec-select2-style' );
        wp_deregister_style( 'mec-font-icons' );
        wp_deregister_style( 'mec-frontend-style' );
        wp_deregister_style( 'mec-tooltip-style' );
        wp_deregister_style( 'mec-tooltip-shadow-style' );
        wp_deregister_style( 'mec-featherlight-style' );
        wp_deregister_style( 'mec-lity-style' );
        wp_deregister_style( 'contact-form-7' );
        wp_deregister_style( 'wpgmp-frontend_css' );
        wp_deregister_style( 'learn-press-bundle' );
        wp_deregister_style( 'learn-press' );
        wp_deregister_style( 'learning-press-style' );
    }

    if (!is_page_template('page-import.php')) {
        wp_deregister_style( 'upload-members-css' );
    }

    if (is_front_page()){
        wp_deregister_style( 'wp-block-library' );
    }
}

add_action( 'wp_enqueue_scripts', 'wpassist_dequeue_scripts' );
function wpassist_dequeue_scripts(){
    if (is_front_page()) {
        wp_deregister_script('wp-embed');
        wp_deregister_script('contact-form-7');

        wp_deregister_script('jquery-ui-datepicker-js-after');
        wp_deregister_script('jquery-ui-datepicker-js');
    }
}


add_action( 'wp_print_scripts',     'my_deregister_scripts', 100 );
function my_deregister_scripts()    {

    if (!is_user_logged_in()){
        wp_deregister_script( 'mec-select2-script' );
        wp_deregister_script( 'mec-typekit-script' );
        wp_deregister_script( 'mec-niceselect-script' );
        wp_deregister_script( 'mec-lity-script' );
        wp_deregister_script( 'mec-nice-scroll' );
        wp_deregister_script( 'mec-featherlight-script' );
        wp_deregister_script( 'mec-owl-carousel-script' );
        wp_deregister_script( 'mec-backend-script' );
        wp_deregister_script( 'mec-events-script' );
        wp_deregister_script( 'mec-frontend-script' );
        wp_deregister_script( 'mec-tooltip-script' );
        wp_deregister_script( 'mec-colorbrightness-script' );
    }

    if (is_front_page()){
        wp_deregister_script( 'lp-plugins-all' );
        wp_deregister_script( 'lp-jquery-plugins' );
        wp_deregister_script( 'global' );
        wp_deregister_script( 'wp-utils' );
        wp_deregister_script( 'learnpress' );
        wp_deregister_script( 'course' );
        wp_deregister_script( 'become-a-teacher' );
        wp_deregister_script('wp-embed');
        wp_deregister_script('contact-form-7');
        wp_deregister_script('jquery-ui-datepicker-js-after');
        wp_deregister_script('jquery-ui-datepicker-js');

    }

    if (!is_page_template('page-import.php')) {
        wp_deregister_script( 'upload-members-js' );
    }

}


function dequeue_jquery_migrate( $scripts ) {
    if ( ! is_admin() && ! empty( $scripts->registered['jquery'] ) ) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            [ 'jquery-migrate' ]
        );
    }
}
add_action( 'wp_default_scripts', 'dequeue_jquery_migrate' );


function load_custom_scripts() {
    if(!is_user_logged_in()){
        wp_deregister_script( 'jquery' );
        wp_register_script('jquery', '//code.jquery.com/jquery-3.5.1.min.js', array(), '3.5.1', true); // true will place script in the footer
        wp_enqueue_script( 'jquery' );
    }
}

if(!is_admin()) {
    add_action('wp_enqueue_scripts', 'load_custom_scripts', 99);
}

function get_user_full_name($userid){
    return get_user_meta($userid,'firstname_user')[0].' '.get_user_meta($userid,'lastname_user')[0];
}

add_action( 'wp_ajax_save_config', 'save_config' );
add_action( 'wp_ajax_nopriv_save_config', 'save_config' );
function save_config() {
    check_ajax_referer( 'update_perfil', 'security' );
    if(is_user_logged_in()){
        if (in_array('administrator', get_current_user_roles())) {
            if ( isset( $_POST['data'] )) {
                parse_str( $_POST['data'], $form_data );
                $postid = sanitize_text_field($form_data['page_id']);
                $correos = sanitize_text_field($form_data['est_email']);
                update_post_meta($postid,'correos_de_notificacion',$correos);
                echo json_encode( [ 'message' => pll__('Cambios actualizados') ] );
                wp_die();
                }
            exit;
            }
        exit;
        }else{

        }
    exit;
 }


add_action( 'savebackups', 'savebackups_cron_function' );

function savebackups_cron_function() {
    $options = array(
        'db_host'=> '127.0.1.1',  //mysql host
        'db_uname' => 'volunteer_user2',  //user
        'db_password' => 'Ed#4b3Mb378vE30Rd', //pass
        'db_to_backup' => 'volunteer2_larkin', //database name
        'db_backup_path' => '/home/volunteer/home/alejandro/public_html_rastreador/wp-content/themes/volunteer/backups', //where to backup
        'db_exclude_tables' => array() //tables to exclude
    );
    $backup_file_name=backup_mysql_database($options);
}

add_action( 'send_mail_correo_estadistica', 'myprefix_cron_function' );

function myprefix_cron_function() {
    $pages = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-estadisticas-email.php'
    ));

    foreach($pages as $page) {
        $correo_exist_msg = get_field('correos_de_notificacion', $page->ID);
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $message_mail_user  = '<img width="300px" height="auto" src="'.get_template_directory_uri() . '/assets/images/logo-black.jpg"><br/><br/>';
        $message_mail_user .= "Estimados Colaboradores,<br/><br/>A continuación les compartimos un resumen diario sobre el comportamiento de nuestros cursos.<br/>Si requieren información más detallada, pueden obtenerla en nuestra sección Estadísticas.<br/><br/>";
        $message_mail_user .= "<strong>Comportamiento Diario por Curso y País</strong><br/><br/>";
        $message_mail_user .= resumen_1();
        $message_mail_user .= "<br/><br/><strong>Comportamiento General Acumulado Por Curso</strong><br/><br/>";
        $message_mail_user .= resumen_2();
        $emails = explode(';', $correo_exist_msg);
        //loop over the emails and check if we need to add cc and bcc addresses
        foreach ($emails as $em) {
            $em = str_replace(' ','',$em);
            if (filter_var($em, FILTER_VALIDATE_EMAIL) == FALSE) {
                $message_mail_user = 'Estimado administrador:<br/>Hubo un problema mientras se enviaba el resumen de estadísticas. <em>'.$em.'</em> no es una dirección de correo válida.';
                wp_mail( get_bloginfo('admin_email'), 'Statistics Summary', $message_mail_user, $headers);
            }else{
                wp_mail( $em, 'Statistics Summary', $message_mail_user, $headers);
            }
        }


    }
}

function resumen_2($start='',$end='') {
    $output = '<table border="1"><tr class="td td-55"><td><strong>'.pll__('Course').'</strong></td><td class="td td-15"><strong>'.pll__('Language').'</strong></td><td class="td td-15"><strong>'.pll__('Matriculados').'</strong></td><td class="td td-15"><strong>'.pll__('Graduados').'</strong></td></tr>';
    $query    = new WP_Query(
        [
            'post_type' => 'lp_course',
            'post_status' => ['publish'],
            'posts_per_page' => 999,
            'lang' => ['es','en','hat'],
        ]
    );
    $events = $query->posts;
    foreach ($events as $event){
        $courseid= $event->ID;
        $titulo = get_the_title($courseid);
        $language = pll_get_post_language($courseid);
        if($language=='en'){
            $language= 'English';
        }elseif($language=='es'){
            $language= 'Español';
        }elseif($language=='hat'){
            $language= 'Kreole';
        }
        $count_studens = 0;
        $count_passed_studens = 0;
        global $wpdb;
        $table_name = $wpdb->prefix . "learnpress_user_items";
        foreach (get_post_meta($courseid,'order-completed')[0] as $key => $el){

            if(empty($start) && empty($end)){
                $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE `ref_id`= %s order by user_item_id desc limit 1", array($el)));
            }elseif(!empty($start) && empty($end)){
                $start = $start.' 00:00:01';
                $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE `ref_id`= %s AND `start_time` >= %s order by user_item_id desc limit 1", array($el,$start)));
            }elseif(empty($start) && !empty($end)){
                $end = $end.' 23:59:59';
                $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE `ref_id`= %s AND `start_time` <= %s order by user_item_id desc limit 1", array($el,$end)));
            }else{
                $start = $start.' 00:00:01';
                $end = $end.' 23:59:59';
                $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE `ref_id`= %s AND `start_time` >= %s AND `start_time` <= %s order by user_item_id desc limit 1", array($el,$start,$end)));
            }
            foreach ($results as $result):
                $user_item_id = $result->user_item_id;
                $table_name1 = $wpdb->prefix . "learnpress_user_itemmeta";
                $results1 = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name1} WHERE `meta_key`= %s AND `learnpress_user_item_id`= %s", array('grade',$user_item_id)));

                $count_studens++;
                foreach ($results1 as $result1):
                    if($result1->meta_value=='passed'){
                        $count_passed_studens++;
                    }
                endforeach;
            endforeach;
        }

        $output .= '<tr><td class="td td-55">'.$titulo.'</td><td class="td td-15">'.$language.'</td><td class="td td-15">'.$count_studens.'</td><td class="td td-15">'.$count_passed_studens.'</td></tr>';
    }

    $output .='</table>';

    return $output;
}

function resumen_1() {

    $fecha_actual = date("d-m-Y");
    $dia_anterior =  date("Y-m-d",strtotime($fecha_actual."- 1 days"));
    $start = $dia_anterior.' 00:00:01';
      $end = $dia_anterior.' 23:59:59';

    $xls_content = '';


        $query    = new WP_Query(
            [
                'post_type' => 'lp_course',
                'post_status' => ['publish'],
                'posts_per_page' => 999,
                'lang' => ['es','en','hat'],
            ]
        );


    $events = $query->posts;
    $count_studens = 0;
    $count_passed_studens = 0;
    $paises = array();
    global $wpdb;
    $table_name = $wpdb->prefix . "learnpress_user_items";
    foreach ($events as $event){
        $courseid= $event->ID;
        $titulo = get_the_title($courseid);
        $language = pll_get_post_language($courseid);
        if($language=='en'){
            $language= 'English';
        }elseif($language=='es'){
            $language= 'Español';
        }elseif($language=='hat'){
            $language= 'Kreole';
        }

        foreach (get_post_meta($courseid,'order-completed')[0] as $key => $el){

            if(empty($start) && empty($end)){
                $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE `ref_id`= %s order by user_item_id desc limit 1", array($el)));
            }elseif(!empty($start) && empty($end)){
                $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE `ref_id`= %s AND `start_time` >= %s order by user_item_id desc limit 1", array($el,$start)));
            }elseif(empty($start) && !empty($end)){
                $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE `ref_id`= %s AND `start_time` <= %s order by user_item_id desc limit 1", array($el,$end)));
            }else{
                $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE `ref_id`= %s AND `start_time` >= %s AND `start_time` <= %s order by user_item_id desc limit 1", array($el,$start,$end)));
            }

            foreach ($results as $result):
                $user_item_id = $result->user_item_id;
                $table_name1 = $wpdb->prefix . "learnpress_user_itemmeta";
                $results1 = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name1} WHERE `meta_key`= %s AND `learnpress_user_item_id`= %s", array('grade',$user_item_id)));

                if(!empty(get_user_meta($result->user_id,'country_user_new', true))){
                    $paisid = get_user_meta($result->user_id,'country_user_new', true);
                    if($paisid=='United States'){ $paisid= 'US'; }
                    if($paisid=='usa'){ $paisid= 'US'; }
                }elseif(!empty(get_user_meta($result->user_id,'country_user', true))){
                    $paisid = get_user_meta($result->user_id,'country_user', true);
                    if($paisid=='United States'){ $paisid= 'US'; }
                    if($paisid=='usa'){ $paisid= 'US'; }
                }else{
                    $paisid = 'zz';
                }
                if(array_key_exists($paisid,$paises[$courseid])){
                    $new_count = $paises[$courseid][$paisid]['matriculados'] + 1;
                    $paises[$courseid][$paisid]['matriculados'] = $new_count;
                }else{
                    $paises[$courseid][$paisid] = array('matriculados' => 1, 'graduados' => 0);
                }
            endforeach;
        }

    }
    $xls_output = '';

    foreach ($paises as $key2 => $curso) {
        $paisescount = 0;
        $matriculados = 0;
        $aprobados = 0;
        $output_body = '';
        $xls_content = '';
        ksort($curso);
        foreach ($curso as $key1 => $pais) {
            $paisescount++;
            $matriculados += $pais['matriculados'];
            if ($key1 == 'zz') {
                $country_name = pll__('Undefined');
            } elseif (empty(get_country_name_by_code($key1))) {
                $country_name = $key1;
            } else {
                $country_name = get_country_name_by_code($key1);
            }
            $xls_content .= '<tr><td>' . $country_name . '</td><td>' . $pais['matriculados'] . '</td></tr>';
        }

        $text_Course = utf8_encode('Países ('.$paisescount.')');
        $text_Language = 'Matriculados ('.$matriculados . ') ';
        $xls_header = '<table border="1">';
        $xls_header .= '<tr>
                                    <th colspan="2"><strong>' . get_the_title($key2) . '</strong></th>
                                </tr><tr>
                                    <th><strong>' . utf8_decode($text_Course) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_Language) . '</strong></th>
                       
                                </tr>';
        $xls_end = '</table><br/><br/>';
        $xls_output .= $xls_header . $xls_content . $xls_end;
    }
    return $xls_output;

}


// Registro de cadenas de texto
pll_register_string( 'Header top ask', 'Have any question?', FALSE );
pll_register_string( 'Contact phone title', 'CONTACT US', FALSE );
pll_register_string( 'Contact phone', '(00) 123 456 789', FALSE );
pll_register_string( 'Contact mail title', 'WRITE SOME WORDS', FALSE );
pll_register_string( 'Contact mail', 'volunteer@larkinhospital.com', FALSE );
pll_register_string( 'Contact address title', 'OUR LOCATION', FALSE );
pll_register_string( 'Contact address', 'Contact address', FALSE );
pll_register_string( 'Login link', 'Login', 'Volunteer', FALSE );
pll_register_string( 'Register link', 'Register', 'Volunteer', FALSE );
pll_register_string( 'My profile link', 'My profile', 'Volunteer', FALSE );
pll_register_string( 'Logout link', 'Logout', 'Volunteer', FALSE );
pll_register_string( 'News title block', 'Latest news', 'Volunteer', FALSE );
pll_register_string( 'News description block', 'Stay informed about the progress of the Covid 19 virus', 'Volunteer', FALSE );

pll_register_string( 'text 1', 'Course', 'Volunteer', FALSE );
pll_register_string( 'text 2', 'Enroll', 'Volunteer', FALSE );
pll_register_string( 'text 3', 'Help eliminate the Covid-19 pandemic from the world.', 'Volunteer', FALSE );
pll_register_string( 'text 4', 'Become a volunteer', 'Volunteer', FALSE );
pll_register_string( 'text 5', 'Latest news', 'Volunteer', FALSE );
pll_register_string( 'text 6', 'Stay informed about the progress of the Covid 19 virus', 'Volunteer', FALSE );
pll_register_string( 'text 7', 'Welcome', 'Volunteer', FALSE );
pll_register_string( 'text 8', 'Personal Information', 'Volunteer', FALSE );
pll_register_string( 'text 9', 'Contact Information', 'Volunteer', FALSE );
pll_register_string( 'text 10', 'Volunteer Information', 'Volunteer', FALSE );
pll_register_string( 'text 11', 'Download', 'Volunteer', FALSE );
pll_register_string( 'text 12', 'Agreement', 'Volunteer', FALSE );
pll_register_string( 'text 12', 'Agreement local', 'Volunteer', FALSE );
pll_register_string( 'text 13', 'CTs Certificate', 'Volunteer', FALSE );
pll_register_string( 'text 13', 'Invalid Agreement local', 'Volunteer', FALSE );
pll_register_string( 'text 13', 'Agreement local required', 'Volunteer', FALSE );
pll_register_string( 'text 14', 'If you passed the course and have your certificate upload it here. In another case, when you finish editing your information, we will give you the opportunity to pass the course on this platform.', 'Volunteer', FALSE );
pll_register_string( 'text 15', 'I agree to the', 'Volunteer', FALSE );
pll_register_string( 'text 16', 'Terms and Conditions', 'Volunteer', FALSE );
pll_register_string( 'text 17', 'First Name', 'Volunteer', FALSE );
pll_register_string( 'text 18', 'Last Name', 'Volunteer', FALSE );
pll_register_string( 'text 19', 'Language', 'Volunteer', FALSE );
pll_register_string( 'text 20', 'Other Language', 'Volunteer', FALSE );
pll_register_string( 'text 21', 'Phone', 'Volunteer', FALSE );
pll_register_string( 'text 22', 'City/Town', 'Volunteer', FALSE );
pll_register_string( 'text 23', 'Province State', 'Volunteer', FALSE );
pll_register_string( 'text 24', 'Country', 'Volunteer', FALSE );
pll_register_string( 'text 25', 'Region', 'Volunteer', FALSE );
pll_register_string( 'text 26', 'Search', 'Volunteer', FALSE );
pll_register_string( 'text 27', 'Not found volunteers.', 'Volunteer', FALSE );
pll_register_string( 'text 28', 'Felicidades. Has aprobado el curso.', 'Volunteer', FALSE );
pll_register_string( 'text 29', '© 2020 Volunteerlarkinhospital. Power by larkinhospital.com', 'Volunteer', FALSE );
pll_register_string( 'text 30', 'Your certificate', 'Volunteer', FALSE );
pll_register_string( 'text 31', 'Find your text', 'Volunteer', FALSE );
pll_register_string( 'text 32', 'Could not update profile. Review the required fields.', 'Volunteer', FALSE );
pll_register_string( 'text 33', 'is required', 'Volunteer', FALSE );
pll_register_string( 'text 34', 'ID required', 'Volunteer', FALSE );
pll_register_string( 'text 35', 'Invalid ID', 'Volunteer', FALSE );
pll_register_string( 'text 36', 'Agreement required', 'Volunteer', FALSE );
pll_register_string( 'text 37', 'Invalid Agreement', 'Volunteer', FALSE );
pll_register_string( 'text 38', 'CTS Certificate required', 'Volunteer', FALSE );
pll_register_string( 'text 39', 'Invalid CTS Certificate', 'Volunteer', FALSE );
pll_register_string( 'text 40', 'First Name required', 'Volunteer', FALSE );
pll_register_string( 'text 41', 'Last Name required', 'Volunteer', FALSE );
pll_register_string( 'text 42', 'Accept Terms and Conditions', 'Volunteer', FALSE );
pll_register_string( 'text 43', 'Select region', 'Volunteer', FALSE );
pll_register_string( 'text 44', 'Close this', 'Volunteer', FALSE );
pll_register_string( 'text 45', "Don't show again", 'Volunteer', FALSE );
pll_register_string( 'text 46', "Events for %s", 'Volunteer', FALSE );
pll_register_string( 'text 47', "No Events", 'Volunteer', FALSE );
pll_register_string( 'text 48', "%s Plaza", 'Volunteer', FALSE );
pll_register_string( 'text 49', "%s Plazas", 'Volunteer', FALSE );
pll_register_string( 'text 50', "Request a place", 'Volunteer', FALSE );
pll_register_string( 'text 51', "Places are sold out", 'Volunteer', FALSE );
pll_register_string( 'text 52', "Place requested", 'Volunteer', FALSE );
pll_register_string( 'text 53', "Event is required", 'Volunteer', FALSE );
pll_register_string( 'text 54', "You are already registered in that event", 'Volunteer', FALSE );
pll_register_string( 'text 55', "Ya estoy listo para Entrevista, Agendar Cita", 'Volunteer', FALSE );
pll_register_string( 'text 56', "Cancel", 'Volunteer', FALSE );
pll_register_string( 'text 57', "Event updated", 'Volunteer', FALSE );
pll_register_string( 'text 58', "Event date is required", 'Volunteer', FALSE );
pll_register_string( 'text 59', "Places must be numeric", 'Volunteer', FALSE );
pll_register_string( 'text 60', "Places is required", 'Volunteer', FALSE );
pll_register_string( 'text 61', "Zoom link must be URL", 'Volunteer', FALSE );
pll_register_string( 'text 62', "Zoom link is required", 'Volunteer', FALSE );
pll_register_string( 'text 63', "Event type is required", 'Volunteer', FALSE );
pll_register_string( 'text 64', "Event created", 'Volunteer', FALSE );
pll_register_string( 'text 65', "Entrenamiento", 'Volunteer', FALSE );
pll_register_string( 'text 66', "Entrevista Final", 'Volunteer', FALSE );
pll_register_string( 'text 67', "Event deleted", 'Volunteer', FALSE );
pll_register_string( 'text 68', "%s solicitud", 'Volunteer', FALSE );
pll_register_string( 'text 69', "%s solicitudes", 'Volunteer', FALSE );
pll_register_string( 'text 70', "Select type", 'Volunteer', FALSE );
pll_register_string( 'text 71', "Cantidad de Plazas", 'Volunteer', FALSE );
pll_register_string( 'text 72', "Zoom link", 'Volunteer', FALSE );
pll_register_string( 'text 73', "Date", 'Volunteer', FALSE );
pll_register_string( 'text 74', "Create event", 'Volunteer', FALSE );
pll_register_string( 'text 75', "Hello %s. Press the More icon to create Training or Final Interview.", 'Volunteer', FALSE );
pll_register_string( 'text 76', "Delete event", 'Volunteer', FALSE );
pll_register_string( 'text 105', "Volunteers", 'Volunteer', FALSE );
pll_register_string( 'text 77', "Gestionar entrenamientos", 'Volunteer', FALSE );
pll_register_string( 'text 78', "Hour", 'Volunteer', FALSE );
pll_register_string( 'text 79', "Coach", 'Volunteer', FALSE );
pll_register_string( 'text 80', "Hour is required", 'Volunteer', FALSE );
pll_register_string( 'text 81', "Coach is required", 'Volunteer', FALSE );
pll_register_string( 'text 82', "Export", 'Volunteer', FALSE );
pll_register_string( 'text 83', "Edit", 'Volunteer', FALSE );
pll_register_string( 'text 84', "Delete", 'Volunteer', FALSE );
pll_register_string( 'text 85', "Request deleted", 'Volunteer', FALSE );
pll_register_string( 'text 86', "Request updated", 'Volunteer', FALSE );
pll_register_string( 'text 87', "Checked", 'Volunteer', FALSE );
pll_register_string( 'text 88', "Yes", 'Volunteer', FALSE );
pll_register_string( 'text 89', "No", 'Volunteer', FALSE );
pll_register_string( 'text 90', "Export result", 'Volunteer', FALSE );
pll_register_string( 'text 91', "User checked", 'Volunteer', FALSE );
pll_register_string( 'text 92', "Date checked", 'Volunteer', FALSE );
pll_register_string( 'text 93', "Action completed. Click the following link to download the updated items.", 'Volunteer', FALSE );
pll_register_string( 'text 94', "Name", 'Volunteer', FALSE );
pll_register_string( 'text 95', "Email", 'Volunteer', FALSE );
pll_register_string( 'text 96', "Region", 'Volunteer', FALSE );
pll_register_string( 'text 97', "Language", 'Volunteer', FALSE );
pll_register_string( 'text 98', "Other language", 'Volunteer', FALSE );
pll_register_string( 'text 99', "Province", 'Volunteer', FALSE );
pll_register_string( 'text 100', "Country", 'Volunteer', FALSE );
pll_register_string( 'text 101', "Select action", 'Volunteer', FALSE );
pll_register_string( 'text 102', "Change to CTS", 'Volunteer', FALSE );
pll_register_string( 'text 103', "Submit", 'Volunteer', FALSE );
pll_register_string( 'text 104', "Remove CTS", 'Volunteer', FALSE );
pll_register_string( 'text 105', "Approved by / Date", 'Volunteer', FALSE );
pll_register_string( 'text 106', "Video de Orientación", 'Volunteer', FALSE );
pll_register_string( 'text 107', "Ya estoy listo para Entrevista, Agendar Cita", 'Volunteer', FALSE );
pll_register_string( 'text 108', "End date", 'Volunteer', FALSE );
pll_register_string( 'text 109', "Start date", 'Volunteer', FALSE );
pll_register_string( 'text 110', "Consejos Finales", 'Volunteer', FALSE );
pll_register_string( 'text 111', "Loading...", 'Volunteer', FALSE );
pll_register_string( 'text 112', "Graduados", 'Volunteer', FALSE );
pll_register_string( 'text 113', "Matriculados", 'Volunteer', FALSE );
pll_register_string( 'text 114', "Undefined", 'Volunteer', FALSE );
pll_register_string( 'text 115', "All", 'Volunteer', FALSE );
pll_register_string( 'text 116', "Generales", 'Volunteer', FALSE );
pll_register_string( 'text 117', "Por países", 'Volunteer', FALSE );
pll_register_string( 'text 118', "Estudiantes por países", 'Volunteer', FALSE );
pll_register_string( 'text 119', "Conf Email", 'Volunteer', FALSE );
pll_register_string( 'text 120', "Correos electrónicos", 'Volunteer', FALSE );
pll_register_string( 'text 120', "Cambios actualizados", 'Volunteer', FALSE );
pll_register_string( 'text 121', "Escriba separado por (;) las direcciones de correo que resivirán la información de estadística.", 'Volunteer', FALSE );
pll_register_string( 'text 116', "No se han encontrado elementos.", 'Volunteer', FALSE );
pll_register_string( 'text 116', "Se ha encontrado 1 elemento.", 'Volunteer', FALSE );
pll_register_string( 'text 116', "Se han encontrado !num elementos.", 'Volunteer', FALSE );
pll_register_string( 'text 116', "Verificado", 'Volunteer', FALSE );
pll_register_string( 'text 116', "!percent% de rendimiento", 'Volunteer', FALSE );
pll_register_string( 'text 116', "No se han encontrado usuarios.", 'Volunteer', FALSE );
pll_register_string( 'text 116', "Se ha encontrado 1 usuario.", 'Volunteer', FALSE );
pll_register_string( 'text 116', "Se han encontrado !num usuarios.", 'Volunteer', FALSE );
pll_register_string( 'text 116', "No se han eliminado usuarios.", 'Volunteer', FALSE );
pll_register_string( 'text 116', "Se ha eliminado 1 usuario.", 'Volunteer', FALSE );
pll_register_string( 'text 116', "Se han eliminado !num usuarios.", 'Volunteer', FALSE );
pll_register_string( 'text 116', "Entrenado", 'Volunteer', FALSE );
pll_register_string( 'text 116', "No entrenado", 'Volunteer', FALSE );
pll_register_string( 'text 116', "Voluntarios entrenados", 'Volunteer', FALSE );
pll_register_string( 'text 116', "Voluntarios no entrenados", 'Volunteer', FALSE );
pll_register_string( 'text 116', "Details", 'Volunteer', FALSE );
pll_register_string( 'text 116', "Aprobado", 'Volunteer', FALSE );

pll_register_string( 'text 116', "January", 'January', FALSE );
pll_register_string( 'text 116', "February", 'February', FALSE );
pll_register_string( 'text 116', "March", 'March', FALSE );
pll_register_string( 'text 116', "April", 'April', FALSE );
pll_register_string( 'text 116', "May", 'May', FALSE );
pll_register_string( 'text 116', "June", 'June', FALSE );
pll_register_string( 'text 116', "July", 'July', FALSE );
pll_register_string( 'text 116', "August", 'August', FALSE );
pll_register_string( 'text 116', "September", 'September', FALSE );
pll_register_string( 'text 116', "October", 'October', FALSE );
pll_register_string( 'text 116', "November", 'November', FALSE );
pll_register_string( 'text 116', "December", 'December', FALSE );

pll_register_string( 'text 116', "SU", 'SU', FALSE );
pll_register_string( 'text 116', "MO", 'MO', FALSE );
pll_register_string( 'text 116', "TU", 'TU', FALSE );
pll_register_string( 'text 116', "WE", 'WE', FALSE );
pll_register_string( 'text 116', "TH", 'TH', FALSE );
pll_register_string( 'text 116', "FR", 'FR', FALSE );
pll_register_string( 'text 116', "SA", 'SA', FALSE );

add_action('wp_ajax_volunteer_courses_mantenimiento', 'volunteer_courses_mantenimiento');
add_action('wp_ajax_nopriv_volunteer_courses_mantenimiento', 'volunteer_courses_mantenimiento');
function volunteer_courses_mantenimiento() {
    check_ajax_referer( 'update_perfil', 'security' );

    $args = [
        'order' => 'ASC',
        'number' => 99999,
        'offset' => 0,
    ];
    $wp_user_query = new WP_User_Query($args);
    $authors = $wp_user_query->get_results();
    $final_count=0;
    $output_body = '';
    foreach ($authors as $user){

        $land = true;
        if (in_array('operador', get_user_roles_($user->ID)) || in_array('administrator', get_user_roles_($user->ID)) || in_array('entrenador', get_user_roles_($user->ID)) || in_array('boss', get_user_roles_($user->ID))) {
            $land = false;
        }
        if($user->ID==1 || $user->ID==15){
            $land = false;
        }
        if(get_user_meta($user->ID,'volunteer_user',TRUE)=='yes' || get_user_meta($user->ID,'matriculado',TRUE)=='yes'){
            $land = false;
        }
        if($land){
            $final_count++;
            $nombre = get_user_full_name($user->ID);
            $email = get_userdata($user->ID)->user_email;
            $paisid = get_user_meta($user->ID, 'country_user_new', true);
            $country_name = get_country_name_by_code($paisid);
            $udata = get_userdata( $user->ID );
            $registered = $udata->user_registered;
            $registereddate = date( "Y-m-d", strtotime( $registered ) );
            $output_body .= '<div class="tr d-flex"><div class="td td-33">'.$nombre.'</div><div class="td td-33">'.$email.'</div><div class="td td-33">'.$registered.'</div></div>';
        }
    }

    if($final_count==0){
        $summary = pll__('No se han encontrado usuarios.');
    }elseif($final_count==1){
        $summary = pll__('se ha encontrado 1 usuario.');
    }else{
        $summary = pll__('Se han encontrado !num usuarios.');
        $summary = str_replace('!num', '<strong>'.$final_count.'</strong>', $summary);
    }
    $output = '<div class="tr d-flex"><div class="td td-33"><strong>'.pll__('Name').'</strong></div><div class="td td-33"><strong>'.pll__('Email').'</strong></div><div class="td td-33"><strong>'.pll__('Registro').'</strong></div></div>';
    $output.=$output_body;
    echo json_encode([
        "html" => $output,
        "summary" => $summary,
    ]);
    exit();
}

add_action('wp_ajax_load_mantenimiento', 'load_mantenimiento');
add_action('wp_ajax_nopriv_load_mantenimiento', 'load_mantenimiento');
function load_mantenimiento() {
    check_ajax_referer( 'update_perfil', 'security' );
    if (isset($_POST['data']) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles())) :
        parse_str($_POST['data'], $form_data);


        $start = $form_data['start_event_date_entrenador'];

        $args = [
            'order' => 'ASC',
            'number' => 99999,
            'offset' => 0,
        ];
        $wp_user_query = new WP_User_Query($args);
        $authors = $wp_user_query->get_results();
        $final_count=0;
        $output_body = '';
        foreach ($authors as $user){

            $land = true;
            if (in_array('operador', get_user_roles_($user->ID)) || in_array('administrator', get_user_roles_($user->ID)) || in_array('entrenador', get_user_roles_($user->ID)) || in_array('boss', get_user_roles_($user->ID))) {
                $land = false;
            }
            if($user->ID==1 || $user->ID==15){
                $land = false;
            }
            if(get_user_meta($user->ID,'volunteer_user',TRUE)=='yes' || get_user_meta($user->ID,'matriculado',TRUE)=='yes'){
                $land = false;
            }
            $udata = get_userdata( $user->ID );
            $registered = $udata->user_registered;
            $registereddate = date( "Y-m-d", strtotime( $registered ) );
            if(!empty($start)) {
                if($registereddate>=$start){
                    $land = false;
                }

            }
            if($land){
                $final_count++;
                $nombre = get_user_full_name($user->ID);
                $email = get_userdata($user->ID)->user_email;
                $paisid = get_user_meta($user->ID, 'country_user_new', true);
                $country_name = get_country_name_by_code($paisid);
                $output_body .= '<div class="tr d-flex"><div class="td td-33">'.$nombre.'</div><div class="td td-33">'.$email.'</div><div class="td td-33">'.$registered.'</div></div>';
            }
        }

        $output = '<div class="tr d-flex"><div class="td td-33"><strong>'.pll__('Name').'</strong></div><div class="td td-33"><strong>'.pll__('Email').'</strong></div><div class="td td-33"><strong>'.pll__('Registro').'</strong></div></div>';
        $output.=$output_body;
        if($final_count==0){
            $summary = pll__('No se han encontrado usuarios.');
        }elseif($final_count==1){
            $summary = pll__('Se ha encontrado 1 usuario.');
        }else{
            $summary = pll__('Se han encontrado !num usuarios.');
            $summary = str_replace('!num', '<strong>'.$final_count.'</strong>', $summary);
        }

        echo json_encode([
            "html" => $output,
            "summary" => $summary,
        ]);
    endif;

    exit();
}

add_action('wp_ajax_limpiar_mantenimiento', 'limpiar_mantenimiento');
add_action('wp_ajax_nopriv_limpiar_mantenimiento', 'limpiar_mantenimiento');
function limpiar_mantenimiento() {
    check_ajax_referer( 'update_perfil', 'security' );
    if (isset($_POST['data']) && is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles())) :
        parse_str($_POST['data'], $form_data);


        $start = $form_data['start_event_date_entrenador'];

        $args = [
            'order' => 'ASC',
            'number' => 99999,
            'offset' => 0,
        ];
        $wp_user_query = new WP_User_Query($args);
        $authors = $wp_user_query->get_results();
        $final_count=0;
        $output_body = '';
        foreach ($authors as $user){

            $land = true;
            if (in_array('operador', get_user_roles_($user->ID)) || in_array('administrator', get_user_roles_($user->ID)) || in_array('entrenador', get_user_roles_($user->ID)) || in_array('boss', get_user_roles_($user->ID))) {
                $land = false;
            }
            if($user->ID==1 || $user->ID==15){
                $land = false;
            }
            if(get_user_meta($user->ID,'volunteer_user',TRUE)=='yes' || get_user_meta($user->ID,'matriculado',TRUE)=='yes'){
                $land = false;
            }
            $udata = get_userdata( $user->ID );
            $registered = $udata->user_registered;
            $registereddate = date( "Y-m-d", strtotime( $registered ) );
            if(!empty($start)) {
                if($registereddate>=$start){
                    $land = false;
                }

            }
            if($land){
               $final_count++;
               wp_delete_user($user->ID);
            }
        }

        if($final_count==0){
            $summary = pll__('No se han eliminado usuarios.');
        }elseif($final_count==1){
            $summary = pll__('Se ha eliminado 1 usuario.');
        }else{
            $summary = pll__('Se han eliminado !num usuarios.');
            $summary = str_replace('!num', '<strong>'.$final_count.'</strong>', $summary);
        }

        echo json_encode([
            "summary" => $summary,
        ]);
    endif;

    exit();
}


add_action('profile_update', 'my_profile_update', 10, 2);
function my_profile_update($user_id, $old_user_data)
{
        if(!empty(get_user_meta($user_id,'matricula_fecha',true))) {
            $pos = strpos(get_user_meta($user_id,'matricula_fecha',true), '-');
            if ($pos === false) {
                $anno = substr(get_user_meta($user_id,'matricula_fecha',true),0,4);
                $mes = substr(get_user_meta($user_id,'matricula_fecha',true),4,2);
                $dia = substr(get_user_meta($user_id,'matricula_fecha',true),6,2);
                update_user_meta($user_id,'matricula_fecha', $anno.'-'.$mes.'-'.$dia);
            }
        }
        if(!empty(get_user_meta($user_id,'matricula_fecha_aprobado',true))) {
            $pos = strpos(get_user_meta($user_id,'matricula_fecha_aprobado',true), '-');
            if ($pos === false) {
                $anno = substr(get_user_meta($user_id,'matricula_fecha_aprobado',true),0,4);
                $mes = substr(get_user_meta($user_id,'matricula_fecha_aprobado',true),4,2);
                $dia = substr(get_user_meta($user_id,'matricula_fecha_aprobado',true),6,2);
                update_user_meta($user_id,'matricula_fecha_aprobado', $anno.'-'.$mes.'-'.$dia);
            }
        }
}


add_action('login_head', function(){
    ?>
    <style>
        #registerform > p:first-child{
            display:none;
        }
    </style>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $('#registerform > p:first-child').css('display', 'none');
        });
    </script>
    <?php
});

add_filter('registration_errors', function($wp_error, $sanitized_user_login, $user_email){
    if(isset($wp_error->errors['empty_username'])){
        unset($wp_error->errors['empty_username']);
    }

    if(isset($wp_error->errors['username_exists'])){
        unset($wp_error->errors['username_exists']);
    }
    return $wp_error;
}, 10, 3);

add_action('login_form_register', function(){
    if(isset($_POST['user_login']) && isset($_POST['user_email']) && !empty($_POST['user_email'])){
        $_POST['user_login'] = $_POST['user_email'];
    }
});

function backup_mysql_database($options){
    $mtables = array(); $contents = "-- Database: `".$options['db_to_backup']."` --\n";

    $mysqli = new mysqli($options['db_host'], $options['db_uname'], $options['db_password'], $options['db_to_backup']);
    if ($mysqli->connect_error) {
        die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }

    $results = $mysqli->query("SHOW TABLES");

    while($row = $results->fetch_array()){
        if (!in_array($row[0], $options['db_exclude_tables'])){
            $mtables[] = $row[0];
        }
    }

    foreach($mtables as $table){
        $contents .= "-- Table `".$table."` --\n";

        $results = $mysqli->query("SHOW CREATE TABLE ".$table);
        while($row = $results->fetch_array()){
            $contents .= $row[1].";\n\n";
        }

        $results = $mysqli->query("SELECT * FROM ".$table);
        $row_count = $results->num_rows;
        $fields = $results->fetch_fields();
        $fields_count = count($fields);

        $insert_head = "INSERT INTO `".$table."` (";
        for($i=0; $i < $fields_count; $i++){
            $insert_head  .= "`".$fields[$i]->name."`";
            if($i < $fields_count-1){
                $insert_head  .= ', ';
            }
        }
        $insert_head .=  ")";
        $insert_head .= " VALUES\n";

        if($row_count>0){
            $r = 0;
            while($row = $results->fetch_array()){
                if(($r % 400)  == 0){
                    $contents .= $insert_head;
                }
                $contents .= "(";
                for($i=0; $i < $fields_count; $i++){
                    $row_content =  str_replace("\n","\\n",$mysqli->real_escape_string($row[$i]));

                    switch($fields[$i]->type){
                        case 8: case 3:
                        $contents .=  $row_content;
                        break;
                        default:
                            $contents .= "'". $row_content ."'";
                    }
                    if($i < $fields_count-1){
                        $contents  .= ', ';
                    }
                }
                if(($r+1) == $row_count || ($r % 400) == 399){
                    $contents .= ");\n\n";
                }else{
                    $contents .= "),\n";
                }
                $r++;
            }
        }
    }

    if (!is_dir ( $options['db_backup_path'] )) {
        mkdir ( $options['db_backup_path'], 0777, true );
    }

    $backup_file_name = $options['db_to_backup'] . " sql-backup- " . date( "d-m-Y--h-i-s").".sql";

    $fp = fopen($options['db_backup_path'] . '/' . $backup_file_name ,'w+');
    if (($result = fwrite($fp, $contents))) {
        echo "Backup file created '--$backup_file_name' ($result)";
    }
    fclose($fp);
    return $backup_file_name;
}

add_action('wp_ajax_export_estadistica_detalle', 'export_estadistica_detalle');
add_action('wp_ajax_nopriv_export_estadistica_detalle', 'export_estadistica_detalle');
function export_estadistica_detalle() {
    if ( is_user_logged_in() && (in_array('administrator', get_current_user_roles()) || in_array('boss', get_current_user_roles())) || in_array('entrenador', get_current_user_roles()) ) {


        $start = $_GET['startdate'];
        $end = $_GET['enddate'];
        $courseide = $_GET['curso'];
        $paiside = $_GET['pais'];
        $xls_content = '';
        $output_body = '';

        $args = [
            'order' => 'ASC',
            'number' => 99999,
            'offset' => 0,
        ];


        $args['meta_query'][] =
            [
                'key' => 'matriculado',
                'value' => 'yes',
                'compare' => '=',
            ];

        if($courseide!=0){
            $args['meta_query'][] =
                [
                    'key' => 'matricula_curso',
                    'value' => $courseide,
                    'compare' => '=',
                ];
        }
        if(!empty($paiside)){
            $args['meta_query'][] =
                [
                    'key' => 'country_user_new',
                    'value' => $paiside,
                    'compare' => '=',
                ];
        }
        if(!empty($start)) {
            $args['meta_query'][] = [
                'key' => 'matricula_fecha',
                'value' => $start,
                'type' => 'date',
                'compare' => '>=',
            ];
        }
        if(!empty($end)) {
            $args['meta_query'][] = [
                'key' => 'matricula_fecha',
                'value' => $end,
                'type' => 'date',
                'compare' => '<=',
            ];
        }

        $wp_user_query = new WP_User_Query($args);
        $authors = $wp_user_query->get_results();
        foreach ($authors as $user){
            $nombre = get_user_full_name($user->ID);
            $email = get_userdata($user->ID)->user_email;
            $paisid = get_user_meta($user->ID, 'country_user_new', true);
            $aprobado = get_user_meta($user->ID, 'matricula_aprobado', true);
            if($aprobado=='yes'){
                $aprobado = pll__('Aprobado');
            }else{
                $aprobado = '';
            }
            $country_name = get_country_name_by_code($paisid);
            $matriculado = get_user_meta($user->ID, 'matricula_fecha', true);
            $xls_content .= '<tr class="tr d-flex"><td class="td td-25">'.utf8_decode($nombre).'</td><td class="td td-25">'.utf8_decode($email).'</td><td class="td td-25">'.utf8_decode($matriculado).'</td><td class="td td-25">'.utf8_decode($aprobado).'</td></tr>';
        }


        $text_Course = pll__('Name');
        $text_Language = pll__('Email');
        $text_Matriculados = pll__('Matriculado');
        $text_Graduados = pll__('Aprobado');
        $xls_header = '<table border="1">';
        $xls_header .= '<tr>
                                    <th><strong>' . utf8_decode($text_Course) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_Language) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_Matriculados) . '</strong></th>
                                    <th><strong>' . utf8_decode($text_Graduados) . '</strong></th>
                                </tr>';
        $xls_end = '</table>';
        $xls_output = $xls_header . $xls_content . $xls_end;
        $file = rand();
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=" . $file . ".xls");
        echo $xls_output;

    }
    exit;
}


/**
 * Save ACF in another database
 *
 * @since Theme 1.0
 */
add_filter( 'acf/settings/save_json', 'my_acf_json_save_point' );
function my_acf_json_save_point( $path ) {
    // update path
    $path = get_stylesheet_directory() . '/vendors/acf';

    // return
    return $path;

}

add_filter( 'acf/settings/load_json', 'my_acf_json_load_point' );
function my_acf_json_load_point( $paths ) {
    unset( $paths[0] );
    $paths[] = get_stylesheet_directory() . '/vendors/acf';

    return $paths;
}

add_action( 'rest_api_init', 'aprobados_ecuador_rest_api');
function aprobados_ecuador_rest_api() {

    register_rest_route( 'curso-entrenamiento-covid19', 'aprobados-ecuador', array(
        'methods' => 'GET',
        'callback' => 'aprobados_ecuador_rest_api_result',
        'permission_callback' => function() {
            return aprobados_ecuador_rest_api_permission();
        },

    ));
}

function aprobados_ecuador_rest_api_permission(){
    return true;
}

function aprobados_ecuador_rest_api_result() {
    $args = [
        'order' => 'ASC',
        'number' => 99999,
        'offset' => 0,
    ];


    $args['meta_query'][] =
        [
            'key' => 'matriculado',
            'value' => 'yes',
            'compare' => '=',
        ];

    $args['meta_query'][] =
        [
            'key' => 'country_user_new',
            'value' => 'EC',
            'compare' => '=',
        ];

    $args['meta_query'][] =
        [
            'key' => 'matricula_aprobado',
            'value' => 'yes',
            'compare' => '=',
        ];

    $wp_user_query = new WP_User_Query($args);
    $authors = $wp_user_query->get_results();
    $user_data = array();
    foreach ($authors as $user){
        $user_id = $user->ID;
        $user_data[ $user_id ][ 'firt_name' ] = get_user_meta($user_id,'firstname_user', TRUE);
        $user_data[ $user_id ][ 'last_name' ] = get_user_meta($user_id,'lastname_user', TRUE);
        $user_data[ $user_id ][ 'email' ] = get_userdata($user_id)->user_email;
    }
    wp_reset_postdata();
    return rest_ensure_response( $user_data );
}

// Deshabilitar la notificación de actualización de plugins
add_filter( 'site_transient_update_plugins', 'dcms_disable_plugin_update' );
function dcms_disable_plugin_update( $value ) {
	if ( isset($value) && is_object($value) ) {
		// Desactivamos las notificaciones del plugin1
		if ( isset( $value->response['learnpress/learnpress.php'] ) ) {
			unset( $value->response['learnpress/learnpress.php'] );
		}
                if ( isset( $value->response['modern-events-calendar-lite/modern-events-calendar-lite.php'] ) ) {
			unset( $value->response['modern-events-calendar-lite/modern-events-calendar-lite.php'] );
		}
	}
	return $value;
}

