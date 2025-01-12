<?php

class farallonSetting
{
    public $config;

    function __construct($config = [])
    {
        $this->config = $config;
        add_action('admin_menu', [$this, 'setting_menu']);
        add_action('admin_enqueue_scripts', [$this, 'setting_scripts']);
        add_action('wp_ajax_farallon_setting', array($this, 'setting_callback'));
        //add_action('wp_ajax_nopriv_farallon_setting', array($this, 'setting_callback'));
    }

    function clean_options(&$value)
    {
        $value = stripslashes($value);
    }

    function setting_callback()
    {
        $data = $_POST[FARALLO_SETTING_KEY];
        array_walk_recursive($data,  array($this, 'clean_options'));
        $this->update_setting($data);
        return wp_send_json([
            'code' => 200,
            'message' => __('Success', 'Farallon'),
            'data' => $this->get_setting()
        ]);
    }

    function setting_scripts()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'farallon') {
            wp_enqueue_style('farallon-setting', get_template_directory_uri() . '/build/css/setting.min.css', array(), FARALLON_VERSION, 'all');
            wp_enqueue_script('farallon-setting', get_template_directory_uri() . '/build/js/setting.min.js', ['jquery'], FARALLON_VERSION, true);
            wp_localize_script(
                'farallon-setting',
                'obvInit',
                [
                    'is_single' => is_singular(),
                    'post_id' => get_the_ID(),
                    'restfulBase' => esc_url_raw(rest_url()),
                    'nonce' => wp_create_nonce('wp_rest'),
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'success_message' => __('Setting saved success!', 'Farallon'),
                ]
            );
        }
    }

    function setting_menu()
    {
        add_menu_page(__('Theme Setting', 'Farallon'), __('Theme Setting', 'Farallon'), 'manage_options', 'farallon', [$this, 'setting_page'], '', 59);
    }

    function setting_page()
    { ?>
        <div class="wrap">
            <h2><?php _e('Theme Setting', 'Farallon') ?></h2>
            <div class="pure-wrap">
                <div class="leftpanel">
                    <ul class="nav">
                        <?php foreach ($this->config['header'] as $val) {
                            $id = $val['id'];
                            $title = __($val['title'], 'Farallon');
                            $icon = $val['icon'];
                            $class = ($id == "basic") ? "active" : "";
                            echo "<li class=\"$class\"><span id=\"tab-title-$id\"><i class=\"dashicons-before dashicons-$icon\"></i>$title</span></li>";
                        } ?>
                    </ul>
                </div>
                <form id="pure-form" method="POST" action="options.php">
                    <?php
                    foreach ($this->config['body'] as $val) {
                        $id = $val['id'];
                        $class = $id == "basic" ? "div-tab" : "div-tab hidden";
                    ?>
                        <div id="tab-<?php echo $id; ?>" class="<?php echo $class; ?>">
                            <table class="form-table">
                                <tbody>
                                    <?php
                                    $content = $val['content'];
                                    foreach ($content as $k => $row) {
                                        switch ($row['type']) {
                                            case 'textarea':
                                                $this->setting_textarea($row);
                                                break;

                                            case 'switch':
                                                $this->setting_switch($row);
                                                break;

                                            case 'input':
                                                $this->setting_input($row);
                                                break;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <div class="pure-save"><span id="pure-save" class="button--save"><?php _e('Save', 'Farallon') ?></span></div>
                </form>
            </div>
        </div>
    <?php }

    function get_setting($key = null)
    {
        $setting = get_option(FARALLO_SETTING_KEY);

        if (!$setting) {
            return false;
        }

        if ($key) {
            if (array_key_exists($key, $setting)) {
                return $setting[$key];
            } else {
                return false;
            }
        } else {
            return $setting;
        }
    }

    function update_setting($setting)
    {
        update_option(FARALLO_SETTING_KEY, $setting);
    }

    function empty_setting()
    {
        delete_option(FARALLO_SETTING_KEY);
    }

    function setting_input($params)
    {
        $default = $this->get_setting($params['name']);
    ?>
        <tr>
            <th scope="row">
                <label for="pure-setting-<?php echo $params['name']; ?>"><?php echo __($params['label'], 'Farallon'); ?></label>
            </th>
            <td>
                <input type="text" id="pure-setting-<?php echo $params['name']; ?>" name="<?php printf('%s[%s]', FARALLO_SETTING_KEY, $params['name']); ?>" value="<?php echo $default; ?>" class="regular-text">
                <?php printf('<br /><br />%s', __($params['description'], 'Farallon')); ?>
            </td>
        </tr>
    <?php }

    function setting_textarea($params)
    { ?>
        <tr>
            <th scope="row">
                <label for="pure-setting-<?php echo $params['name']; ?>"><?php echo __($params['label'], 'Farallon'); ?></label>
            </th>
            <td>
                <textarea name="<?php printf('%s[%s]', FARALLO_SETTING_KEY, $params['name']); ?>" id="pure-setting-<?php echo $params['name']; ?>" class="large-text code" rows="5" cols="50"><?php echo $this->get_setting($params['name']); ?></textarea>
                <?php printf('<br />%s', __($params['description'], 'Farallon')); ?>
            </td>
        </tr>
    <?php }

    function setting_switch($params)
    {
        $val = $this->get_setting($params['name']);
        $val = $val ? 1 : 0;
    ?>
        <tr>
            <th scope="row">
                <label for="pure-setting-<?php echo $params['name']; ?>"><?php echo __($params['label'], 'Farallon'); ?></label>
            </th>
            <td>
                <a class="pure-setting-switch<?php if ($val) echo ' active'; ?>" href="javascript:;" data-id="pure-setting-<?php echo $params['name']; ?>">
                    <i></i>
                </a>
                <br />
                <input type="hidden" id="pure-setting-<?php echo $params['name']; ?>" name="<?php printf('%s[%s]', FARALLO_SETTING_KEY, $params['name']); ?>" value="<?php echo $val; ?>" class="regular-text">
                <?php printf('<br />%s', __($params['description'], 'Farallon')); ?>
            </td>
        </tr>
<?php }
}
global $farallonSetting;
$farallonSetting = new farallonSetting(
    [
        "header" => [
            [
                'id' => 'basic',
                'title' => __('Basic Setting', 'Farallon'),
                'icon' => 'basic'
            ],
            [
                'id' => 'feature',
                'title' => __('Feature Setting', 'Farallon'),
                'icon' => 'slider'

            ],
            [
                'id' => 'singluar',
                'title' => __('Singluar Setting', 'Farallon'),
                'icon' => 'feature'
            ],
            [
                'id' => 'meta',
                'title' => __('SNS Setting', 'Farallon'),
                'icon' => 'interface'
            ],
            [
                'id' => 'custom',
                'title' => __('Custom Setting', 'Farallon'),
                'icon' => 'social-contact'
            ]
        ],
        "body" => [
            [
                'id' => 'basic',
                'content' => [
                    [
                        'type' => 'textarea',
                        'name' => 'description',
                        'label' => __('Description', 'Farallon'),
                        'description' => __('Site description', 'Farallon'),
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'headcode',
                        'label' => __('Headcode', 'Farallon'),
                        'description' => __('You can add content to the head tag, such as site verification tags, and so on.', 'Farallon'),
                    ],
                    [
                        'type' => 'input',
                        'name' => 'logo',
                        'label' => __('Logo', 'Farallon'),
                        'description' => __('Logo address, preferably in a square shape.', 'Farallon'),
                    ],
                    [
                        'type' => 'input',
                        'name' => 'favicon',
                        'label' => __('Favicon', 'Farallon'),
                        'description' => __('Favicon address', 'Farallon'),
                    ],
                    [
                        'type' => 'input',
                        'name' => 'title_sep',
                        'label' => __('Title sep', 'Farallon'),
                        'description' => __('Default is', 'Farallon') . '<code>-</code>',
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'disable_block_css',
                        'label' => __('Disable block css', 'Farallon'),
                        'description' => __('Do not load block-style files.', 'Farallon')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'gravatar_proxy',
                        'label' => __('Gravatar proxy', 'Farallon'),
                        'description' => __('Gravatar proxy domain,like <code>cravatar.cn</code>', 'Farallon'),
                    ],
                ]
            ],
            [
                'id' => 'feature',
                'content' => [
                    [
                        'type' => 'switch',
                        'name' => 'upyun',
                        'label' => __('Upyun CDN', 'Farallon'),
                        'description' => __('Make sure all images are uploaded to Upyun, otherwise thumbnails may not display properly.', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'oss',
                        'label' => __('Aliyun OSS CDN', 'Farallon'),
                        'description' => __('Make sure all images are uploaded to Aliyun OSS, otherwise thumbnails may not display properly.', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'qiniu',
                        'label' => __('Qiniu OSS CDN', 'Farallon'),
                        'description' => __('Make sure all images are uploaded to Qiniu OSS, otherwise thumbnails may not display properly.', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'darkmode',
                        'label' => __('Dark Mode', 'Farallon'),
                        'description' => __('Enable dark mode', 'Farallon')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'default_thumbnail',
                        'label' => __('Default thumbnail', 'Farallon'),
                        'description' => __('Default thumbnail address', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'back2top',
                        'label' => __('Back to top', 'Farallon'),
                        'description' => __('Enable back to top', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'loadmore',
                        'label' => __('Load more', 'Farallon'),
                        'description' => __('Enable load more', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'home_author',
                        'label' => __('Author info', 'Farallon'),
                        'description' => __('Enable author info in homepage', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'home_cat',
                        'label' => __('Category info', 'Farallon'),
                        'description' => __('Enable category info in homepage', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'home_like',
                        'label' => __('Like info', 'Farallon'),
                        'description' => __('Enable like info in homepage', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'hide_home_cover',
                        'label' => __('Hide home cover', 'Farallon'),
                        'description' => __('Hide home cover', 'Farallon')
                    ],
                ]
            ],

            [
                'id' => 'singluar',
                'content' => [
                    [
                        'type' => 'switch',
                        'name' => 'bio',
                        'label' => __('Author bio', 'Farallon'),
                        'description' => __('Enable author bio', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'related',
                        'label' => __('Related posts', 'Farallon'),
                        'description' => __('Enable related posts', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'postlike',
                        'label' => __('Post like', 'Farallon'),
                        'description' => __('Enable post like', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'post_navigation',
                        'label' => __('Post navigation', 'Farallon'),
                        'description' => __('Enable post navigation', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'show_copylink',
                        'label' => __('Copy link', 'Farallon'),
                        'description' => __('Enable copy link', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'show_parent',
                        'label' => __('Show parent comment', 'Farallon'),
                        'description' => __('Enable show parent comment', 'Farallon')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'toc',
                        'label' => __('Table of content', 'Farallon'),
                        'description' => __('Enable table of content', 'Farallon')
                    ],

                ]
            ],
            [
                'id' => 'meta',
                'content' => [
                    [
                        'type' => 'input',
                        'name' => 'telegram',
                        'label' => __('Telegram', 'Farallon'),
                        'description' => __('Telegram link', 'Farallon')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'telegram_group',
                        'label' => __('Telegram group', 'Farallon'),
                        'description' => __('Telegram group link', 'Farallon')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'telegram_channel',
                        'label' => __('Telegram channel', 'Farallon'),
                        'description' => __('Telegram channel link', 'Farallon')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'instagram',
                        'label' => __('Instagram', 'Farallon'),
                        'description' => __('Instagram link', 'Farallon')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'twitter',
                        'label' => __('Twitter', 'Farallon'),
                        'description' => __('Twitter link', 'Farallon')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'rss',
                        'label' => __('RSS', 'Farallon'),
                        'description' => __('RSS link', 'Farallon')
                    ],
                ]
            ],
            [
                'id' => 'custom',
                'content' => [
                    [
                        'type' => 'textarea',
                        'name' => 'css',
                        'label' => __('CSS', 'Farallon'),
                        'description' => __('Custom CSS', 'Farallon')
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'javascript',
                        'label' => __('Javascript', 'Farallon'),
                        'description' => __('Custom Javascript', 'Farallon')
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'copyright',
                        'label' => __('Copyright', 'Farallon'),
                        'description' => __('Custom footer content', 'Farallon')
                    ],
                ]
            ],
        ]
    ]
);
