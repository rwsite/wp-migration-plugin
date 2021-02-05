<?php
/**
 * Admin interface
 * @var array $migrations
 */

?>
<div class="wrap">
    <h2><?=get_admin_page_title();?></h2>

    <div id="message" class="notice is-dismissible" style="display: none;">
        <div class="notice-content">
            <!-- JS notice content HERE -->
        </div>
    </div>

    <form action="" method="post">
        <div class="container">
            <?php foreach ($migrations as $migration):
                if('migrations\plugin' === $migration){
                    continue;
                }
                if(class_exists($migration)) {
                    $exemplar = new $migration();
                    $className = str_replace('migrations' . '\\', '', $migration);
                    ?>
                    <div>

                        <?php if(!empty($exemplar->title)): ?>
                            <h4><?php echo esc_html($exemplar->title); ?></h4>
                        <?php else: ?>
                            <h4><?php echo $className; ?></h4>
                        <?php endif ; ?>

                        <?php if(!empty($exemplar->description)): ?>
                            <p><?php echo esc_html($exemplar->description) ?></p>
                        <?php endif ; ?>

                        <a href="#" class="migration button button-primary" name="<?=$className?>">Run this migration</a>
                    </div>
                <?php } ?>
            <?php endforeach; ?>
        </div>
    </form>
</div>

<style>
    .container {
        display: flex;
        flex-wrap: wrap;
    }

    .container div {
        margin: 5px;
        width: calc(20% - 5 * 2px);
        padding: 15px 10px;
        background: white;
        color: #23282d;
        transition: box-shadow .3s ease-in-out;
    }
    .container div:hover {
        box-shadow: 0 24px 36px -11px rgb(0 0 0 / 35%);
    }

    .container div h4 {
        font-size: 1em;
        margin: 0 0 10px;
    }

    @media (max-width: 800px) and (min-width: 500px) {
        .container div {
            width: calc(50% - 15 * 2px);
        }
    }

    @media (max-width: 500px) {
        .container div {
            width: calc(100% - 2 * 2px);
        }
    }
</style>

<script>
    jQuery(document).ready(function($) {

        $('.migration').each(function (k,value){

            $(this).click(function () {

                let current;
                let flag = $(this).attr('disabled');

                if ('disabled' === flag) {
                    return;
                }

                let cls = $(this).attr('name');

                $(this).attr('disabled', 'true');
                show_message('<?php _e('Loading ... This may take a long time. Don`t refresh this page.', 'wp-migration'); ?>', 'info', true);

                current = {start: 0};
                function run_import( current ) {
                    $.ajax({
                        url: '/wp-admin/admin-ajax.php',
                        method: "POST",
                        data: {
                            action: cls + '_ajax',
                            data:   current
                        }
                    }).done(function (data) {
                        data = JSON.parse(data);

                        console.log(data);
                        console.log( 'php result: ' + data.current + ' from ' + data.all );

                        if( data.message !== undefined ) {
                            $('#message .usage').remove();
                            $('#message').append( '<div class="usage"><p>' + data.message + '<p></div>' );
                        }

                        if ( data.current !== data.all ) {
                            show_message( 'Checked: ' + data.current + ' from ' + data.all );
                            current = {start: data.current};
                            setTimeout( run_import(current), 0);// рекурсивный запуск загрузки
                        } else {
                            $(this).removeAttr('disabled');
                            show_message( '<?php _e('Migration', 'wp-migration')?> ' + cls + ' <?php _e('completed successfully!', 'wp-migration')?>', 'success' );
                        }

                    });
                }
                run_import(current);
            });
        });


        /**
         * Showing notice
         *
         * @param html string
         * @param lvl string
         * @param loader (bool)
         */
        function show_message(html, lvl = 'info', loader = false){
            let str = ''
            try {
                html = JSON.parse(html);
                html.forEach((element) => {
                    str += '<p>'+element+'</p>';
                })
            } catch (e) {
                str = '<p>'+html+'</p>';
            }

            if(loader){
                str = '<div class="spinner is-active" ' +
                    'style="float: left;width: auto;height: auto;padding: 10px 0 10px 20px;"></div>' + str;
            }

            $('#message').show().addClass('notice-'+ lvl);
            $('.notice-content *').detach();
            $('.notice-content').append( str );
        }

    });
</script>