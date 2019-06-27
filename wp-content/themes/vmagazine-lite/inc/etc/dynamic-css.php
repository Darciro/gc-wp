<?php
/**
* Dynamic css file for the theme
*
*/
function vmagazine_lite_dynamic_css(){

    $custom_css = "";

    /**
    * Categories color
    *
    *
    */
     global $vmagazine_cat_array;
     if( $vmagazine_cat_array ):
         foreach ( $vmagazine_cat_array as $key => $value ) {
            $cat_color = get_theme_mod('vmagazine_cat_color_' . $key, '#e52d6d');

            $custom_css .="
            span.cat-links .cat-$key{
                    background: {$cat_color};
            }";
         }
    endif;

/**
* Mobile Navigation options
*
*/
$vmagazine_mobile_header_bg_color = get_theme_mod('vmagazine_mobile_header_bg_color');
$vmagazine_mobile_header_bg = get_theme_mod('vmagazine_mobile_header_bg');
$vmagazine_mobile_header_bg_repeat = get_theme_mod('vmagazine_mobile_header_bg_repeat','no-repeat');

if( $vmagazine_mobile_header_bg ){
    $custom_css .="
        .mob-search-form,.mobile-navigation{
            background-image: url(".esc_url($vmagazine_mobile_header_bg).");
            background-repeat: {$vmagazine_mobile_header_bg_repeat};
        }"; 

    $custom_css .="
        .vmagazine-mobile-search-wrapper .mob-search-form .img-overlay,.vmagazine-mobile-navigation-wrapper .mobile-navigation .img-overlay{
            background-color: {$vmagazine_mobile_header_bg_color};
        }";    
     
}else{
    $custom_css .="
        .mob-search-form,.mobile-navigation{
            background-color: {$vmagazine_mobile_header_bg_color};
        }";   
}
    
/**
* Theme color
*
*/
$vmagazine_theme_color = get_theme_mod('vmagazine_theme_color','#e52d6d');
$rgba_theme_color = vmagazine_lite_hex2rgba( $vmagazine_theme_color, 0.6 );
$clrgba_theme_color = vmagazine_lite_hex2rgba( $vmagazine_theme_color, 0.3 );
$theme_title_color = vmagazine_lite_hex2rgba( $vmagazine_theme_color, 0.2 );

if( $vmagazine_theme_color != '#e52d6d' ){
    $custom_css .="
    .vmagazine-ticker-wrapper .default-layout .vmagazine-ticker-caption span, 
    .vmagazine-ticker-wrapper .layout-two .vmagazine-ticker-caption span,
    header.header-layout4 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item a:hover,
    a.scrollup,a.scrollup:hover,.widget .tagcloud a:hover,span.cat-links a,.entry-footer .edit-link a.post-edit-link,
    .template-three .widget-title:before, .template-three .block-title:before,.template-three .widget-title span, .template-three .block-title span,.widget-title:after, .block-title:after,
    .template-four .widget-title span, .template-four .block-title span, .template-four .vmagazine-container #primary.vmagazine-content .vmagazine-related-wrapper h4.related-title span.title-bg, .template-four .comment-respond h4.comment-reply-title span, .template-four .vmagazine-container #primary.vmagazine-content .post-review-wrapper h4.section-title span,.template-five .widget-title:before, .template-five .block-title:before,
    .template-five .widget-title span, .template-five .block-title span,.vmagazine-archive-layout2 .vmagazine-container main.site-main article .archive-post .entry-content a.vmagazine-archive-more, .vmagazine-archive-layout2 .vmagazine-container main.site-main article .archive-post .entry-content a.vmagazine-archive-more, .vmagazine-archive-layout2 .vmagazine-container main.site-main article .archive-post .entry-content a.vmagazine-archive-more,.vmagazine-container #primary.vmagazine-content .vmagazine-related-wrapper h4.related-title:after, .vmagazine-container #primary.vmagazine-content .post-review-wrapper .section-title:after, .vmagazine-container #primary.vmagazine-content .comment-respond .comment-reply-title:after,
    .vmagazine-container #primary.vmagazine-content .comment-respond .comment-form .form-submit input.submit,.widget .custom-html-widget .tnp-field-button input.tnp-button,.woocommerce-page .vmagazine-container.sidebar-shop .widget_price_filter .ui-slider .ui-slider-range,.woocommerce-page .vmagazine-container.sidebar-shop ul.products li.product .product-img-wrap a.button,.woocommerce-page .vmagazine-container.sidebar-shop ul.products li.product .onsale, .sidebar-shop .sale span.onsale,.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt,.woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover,header ul.site-header-cart li span.count,
    header ul.site-header-cart li.cart-items .widget_shopping_cart p.woocommerce-mini-cart__buttons a.button:hover,
    .widget .tagcloud a:hover, .top-footer-wrap .vmagazine-container .widget.widget_tag_cloud .tagcloud a:hover,
    header.header-layout3 .site-main-nav-wrapper .top-right .vmagazine-search-form-primary form.search-form label:before,
    .vmagazine-archive-layout1 .vmagazine-container #primary article .archive-wrapper .entry-content a.vmagazine-archive-more,
    .vmagazine-container #primary.vmagazine-content .entry-content nav.post-navigation .nav-links a:hover:before,
    .vmagazine-archive-layout4 .vmagazine-container #primary article .entry-content a.vmagazine-archive-more,
    header.header-layout2 .logo-ad-wrapper .middle-search form.search-form:after,
    .ap_toggle .ap_toggle_title,.ap_tagline_box.ap-bg-box,.ap-team .member-social-group a, .horizontal .ap_tab_group .tab-title.active, .horizontal .ap_tab_group .tab-title.hover, .vertical .ap_tab_group .tab-title.active, .vertical .ap_tab_group .tab-title.hover,
    .template-three .vmagazine-container #primary.vmagazine-content .post-review-wrapper h4.section-title span, .template-three .vmagazine-container #primary.vmagazine-content .vmagazine-related-wrapper h4.related-title span, .template-three .vmagazine-container #primary.vmagazine-content .comment-respond h4.comment-reply-title span, .template-three .vmagazine-container #primary.vmagazine-content .post-review-wrapper h4.section-title span.title-bg,
    .template-three .vmagazine-container #primary.vmagazine-content .post-review-wrapper h4.section-title:before, .template-three .vmagazine-container #primary.vmagazine-content .vmagazine-related-wrapper h4.related-title:before, .template-three .vmagazine-container #primary.vmagazine-content .comment-respond h4.comment-reply-title:before, .template-three .vmagazine-container #primary.vmagazine-content .post-review-wrapper h4.section-title:before,
    .vmagazine-container #primary.vmagazine-content .post-password-form input[type='submit'],
    .woocommerce .cart .button, .woocommerce .cart input.button,
    .dot_1,.vmagazine-grid-list.list #loading-grid .dot_1,
    span.view-all a:hover,.block-post-wrapper.block_layout_3 .view-all a:hover,
    .vmagazine-post-col.block_layout_1 span.view-all a:hover,
    .vmagazine-mul-cat.block-post-wrapper.layout-two .block-content-wrapper .right-posts-wrapper .view-all a:hover,
    .block-post-wrapper.list .gl-posts a.vm-ajax-load-more:hover, .block-post-wrapper.grid-two .gl-posts a.vm-ajax-load-more:hover,
    .vmagazine-cat-slider.block-post-wrapper.block_layout_1 .content-wrapper-featured-slider .lSSlideWrapper li.single-post .post-caption p span.read-more a,.template-five .vmagazine-container #primary.vmagazine-content .comment-respond .comment-reply-title span.title-bg,
    .template-three .vmagazine-container #primary.vmagazine-content .vmagazine-author-metabox h4.box-title span.title-bg,
    .template-three .vmagazine-container #primary.vmagazine-content .vmagazine-author-metabox h4.box-title::before,
    .vmagazine-container #primary.vmagazine-content .vmagazine-author-metabox .box-title::after,
    .template-five .vmagazine-container #primary.vmagazine-content .vmagazine-related-wrapper h4.related-title span.title-bg,
    .template-five .vmagazine-container #primary.vmagazine-content .vmagazine-author-metabox .box-title span.title-bg,
    .middle-search .block-loader .dot_1,.no-results.not-found form.search-form input.search-submit,
    .widget_vmagazine_categories_tabbed .vmagazine-tabbed-wrapper ul#vmagazine-widget-tabbed li.active a, .widget_vmagazine_categories_tabbed .vmagazine-tabbed-wrapper ul#vmagazine-widget-tabbed li a:hover,
    .vmagazine-container #primary .entry-content .post-tag .tags-links a,
    .vmagazine-cat-slider.block-post-wrapper.block_layout_1 .lSSlideWrapper .lSAction > a:hover,
    .related-content-wrapper a.vmagazine-related-more,
    .vmagazine-container #primary .post-review-wrapper .review-inner-wrap .percent-review-wrapper .percent-rating-bar-wrap div, .vmagazine-container #primary .post-review-wrapper .review-inner-wrap .points-review-wrapper .percent-rating-bar-wrap div,
    .vmagazine-fullwid-slider.block_layout_1 .slick-slider .post-content-wrapper h3.extra-large-font a:hover,
    .vmagazine-post-carousel.block_layout_2 .block-carousel .single-post:hover .post-caption h3.large-font a,
    .vmagazine-container #primary .comment-respond .comment-reply-title::after
    {
        background: $vmagazine_theme_color;
    }";

    $custom_css .="
    a:hover,.vmagazine-ticker-wrapper .layout-two .ticker-tags ul li a:hover,
    header.header-layout2 nav.main-navigation .nav-wrapper .index-icon a:hover, header.header-layout1 nav.main-navigation .nav-wrapper .index-icon a:hover, header.header-layout3 nav.main-navigation .nav-wrapper .index-icon a:hover, header.header-layout4 nav.main-navigation .nav-wrapper .index-icon a:hover,
    .widget.widget_categories ul li,.widget.widget_categories ul li a:hover,footer .buttom-footer.footer_one .footer-credit .footer-social ul.social li a:hover,header.header-layout4 .logo-wrapper-section .vmagazine-container .social-icons ul.social li a:hover,header.header-layout2 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu li a:hover, header.header-layout1 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu li a:hover, header.header-layout3 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu li a:hover, header.header-layout4 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu li a:hover,header.header-layout2 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu.mega-sub-menu .ap-mega-menu-con-wrap .cat-con-section .menu-post-block h3 a:hover, header.header-layout1 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu.mega-sub-menu .ap-mega-menu-con-wrap .cat-con-section .menu-post-block h3 a:hover, header.header-layout3 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu.mega-sub-menu .ap-mega-menu-con-wrap .cat-con-section .menu-post-block h3 a:hover, header.header-layout4 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu.mega-sub-menu .ap-mega-menu-con-wrap .cat-con-section .menu-post-block h3 a:hover,.vmagazine-breadcrumb-wrapper .vmagazine-bread-home span.current,.vmagazine-container .vmagazine-sidebar .widget.widget_archive ul li,.vmagazine-container .vmagazine-sidebar .widget.widget_archive ul li a:hover,
    .vmagazine-container .vmagazine-sidebar .widget.widget_nav_menu .menu-main-menu-container ul li a:hover, .vmagazine-container .vmagazine-sidebar .widget.widget_rss ul li a:hover, .vmagazine-container .vmagazine-sidebar .widget.widget_recent_entries ul li a:hover, .vmagazine-container .vmagazine-sidebar .widget.widget_meta ul li a:hover, .vmagazine-container .vmagazine-sidebar .widget.widget_pages ul li a:hover,.site-footer .footer-widgets .widget_vmagazine_info .footer_info_wrap .info_wrap div span:first-of-type,
    .vmagazine-container #primary.vmagazine-content .entry-content nav.post-navigation .nav-links a:hover p,
    .vmagazine-container #primary.vmagazine-content .post-review-wrapper .review-inner-wrap .summary-wrapper .total-reivew-wrapper span.stars-count,.vmagazine-container #primary.vmagazine-content .post-review-wrapper .review-inner-wrap .stars-review-wrapper .review-featured-wrap span.stars-count span.star-value,header.header-layout1 .vmagazine-top-header .top-menu ul li a:hover, header.header-layout3 .vmagazine-top-header .top-menu ul li a:hover,header.header-layout1 .vmagazine-top-header .top-left ul.social li a:hover, header.header-layout3 .vmagazine-top-header .top-right ul.social li a:hover,header.header-layout1 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item a:hover, header.header-layout3 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item a:hover,header.header-layout2 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu li.menu-item.menu-item-has-children:hover:after, header.header-layout1 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu li.menu-item.menu-item-has-children:hover:after, header.header-layout3 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu li.menu-item.menu-item-has-children:hover:after, header.header-layout4 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu li.menu-item.menu-item-has-children:hover:after,header.header-layout2 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu li .menu-post-block:hover a, header.header-layout1 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu li .menu-post-block:hover a, header.header-layout3 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu li .menu-post-block:hover a, header.header-layout4 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item .sub-menu li .menu-post-block:hover a,header.header-layout2 nav.main-navigation .nav-wrapper .menu-mmnu-container ul li.menu-item:hover a,.woocommerce-page .vmagazine-container.sidebar-shop ul.products li.product:hover a.woocommerce-LoopProduct-link h2,.woocommerce-page .vmagazine-container.sidebar-shop ul.products span.price,.woocommerce-page .vmagazine-container.sidebar-shop .vmagazine-sidebar .widget_product_categories .product-categories li,.woocommerce-page .vmagazine-container.sidebar-shop .vmagazine-sidebar .widget_product_categories .product-categories li a:hover,.woocommerce-page .vmagazine-container.sidebar-shop .widget_top_rated_products ul.product_list_widget li ins span.woocommerce-Price-amount, .woocommerce-page .vmagazine-container.sidebar-shop .widget_recent_reviews ul.product_list_widget li ins span.woocommerce-Price-amount,.woocommerce-page .vmagazine-container.sidebar-shop .widget_top_rated_products ul.product_list_widget li:hover a, .woocommerce-page .vmagazine-container.sidebar-shop .widget_recent_reviews ul.product_list_widget li:hover a,.woocommerce div.product p.price, .woocommerce div.product span.price,.comment-form-rating p.stars,header ul.site-header-cart li.cart-items .widget_shopping_cart p.woocommerce-mini-cart__buttons a.button,footer .buttom-footer.footer_one .footer-btm-wrap .vmagazine-btm-ftr .footer-nav ul li a:hover,
    .vmagazine-container .vmagazine-sidebar .widget.widget_nav_menu ul li, .vmagazine-container .vmagazine-sidebar .widget.widget_rss ul li, .vmagazine-container .vmagazine-sidebar .widget.widget_recent_entries ul li, .vmagazine-container .vmagazine-sidebar .widget.widget_recent_comments ul li, .vmagazine-container .vmagazine-sidebar .widget.widget_meta ul li, .vmagazine-container .vmagazine-sidebar .widget.widget_pages ul li, .top-footer-wrap .vmagazine-container .widget.widget_meta ul li, .top-footer-wrap .vmagazine-container .widget.widget_pages ul li, .top-footer-wrap .vmagazine-container .widget.widget_recent_comments ul li, .top-footer-wrap .vmagazine-container .widget.widget_recent_entries ul li, .top-footer-wrap .vmagazine-container .widget.widget_rss ul li, .top-footer-wrap .vmagazine-container .widget.widget_nav_menu ul li, .top-footer-wrap .vmagazine-container .widget.widget_archive ul li,
    .vmagazine-container .vmagazine-sidebar .widget.widget_nav_menu ul li a:hover, .vmagazine-container .vmagazine-sidebar .widget.widget_rss ul li a:hover, .vmagazine-container .vmagazine-sidebar .widget.widget_recent_entries ul li a:hover, .vmagazine-container .vmagazine-sidebar .widget.widget_meta ul li a:hover, .vmagazine-container .vmagazine-sidebar .widget.widget_pages ul li a:hover, .top-footer-wrap .vmagazine-container .widget_pages ul li a:hover, .top-footer-wrap .vmagazine-container .widget.widget_meta ul li a:hover, .top-footer-wrap .vmagazine-container .widget.widget_pages ul li a:hover, .top-footer-wrap .vmagazine-container .widget.widget_recent_comments ul li a:hover, .top-footer-wrap .vmagazine-container .widget.widget_recent_entries ul li a:hover, .top-footer-wrap .vmagazine-container .widget.widget_rss ul li a:hover, .top-footer-wrap .vmagazine-container .widget.widget_nav_menu ul li a:hover, .top-footer-wrap .vmagazine-container .widget.widget_archive ul li a:hover,
    .vmagazine-archive-layout2 .vmagazine-container main.site-main article .archive-post .entry-content a.vmagazine-archive-more:hover, .vmagazine-archive-layout2 .vmagazine-container main.site-main article .archive-post .entry-content a.vmagazine-archive-more:hover, .vmagazine-archive-layout2 .vmagazine-container main.site-main article .archive-post .entry-content a.vmagazine-archive-more:hover,
    .vmagazine-archive-layout1 .vmagazine-container #primary article .archive-wrapper .entry-content a.vmagazine-archive-more:hover,
    .vmagazine-container #primary.vmagazine-content .post-password-form input[type='submit']:hover,
    .vmagazine-archive-layout4 .vmagazine-container #primary article .entry-content a.vmagazine-archive-more:hover,
    .vmagazine-container #primary .entry-content .post-tag .tags-links a:hover,
    .vmagazine-archive-layout2 .vmagazine-container main.site-main article .archive-post .entry-content a.vmagazine-archive-more:hover::after,
    .vmagazine-slider-tab-carousel .block-content-wrapper-carousel .single-post:hover .post-caption h3,
    .woocommerce-page .vmagazine-container.sidebar-shop .widget_top_rated_products ul.product_list_widget li:hover a,
    .woocommerce-page .vmagazine-container.sidebar-shop .widget_recently_viewed_products ul.product_list_widget li:hover a,
    .woocommerce-page .vmagazine-container.sidebar-shop .widget_products ul.product_list_widget li:hover a,
    .woocommerce-page .vmagazine-container.sidebar-shop .widget_recent_reviews ul.product_list_widget li:hover a,
    .related-content-wrapper a.vmagazine-related-more:hover
    {
        color: $vmagazine_theme_color;
    }";

    $custom_css .="
    .widget .tagcloud a:hover,.vmagazine-container .vmagazine-sidebar .widget.widget_search form.search-form input.search-field:focus,.site-footer .footer-widgets .widget .tagcloud a:hover,header ul.site-header-cart li.cart-items .widget_shopping_cart p.woocommerce-mini-cart__buttons a.button,.widget .tagcloud a:hover, .top-footer-wrap .vmagazine-container .widget.widget_tag_cloud .tagcloud a:hover,
    .vmagazine-container #primary.vmagazine-content .entry-content nav.post-navigation .nav-links a:hover:before,
    .vmagazine-archive-layout2 .vmagazine-container main.site-main article .archive-post .entry-content a.vmagazine-archive-more, .vmagazine-archive-layout2 .vmagazine-container main.site-main article .archive-post .entry-content a.vmagazine-archive-more, .vmagazine-archive-layout2 .vmagazine-container main.site-main article .archive-post .entry-content a.vmagazine-archive-more,
    .ap_toggle,.ap_tagline_box.ap-all-border-box,.ap_tagline_box.ap-left-border-box,
    .vmagazine-archive-layout4 .vmagazine-container #primary article .entry-content a.vmagazine-archive-more,
    .vmagazine-archive-layout1 .vmagazine-container #primary article .archive-wrapper .entry-content a.vmagazine-archive-more,
    .vmagazine-container #primary.vmagazine-content .post-password-form input[type='submit'],
    .vmagazine-container #primary.vmagazine-content .post-password-form input[type='submit']:hover,
    .vmagazine-archive-layout2 .vmagazine-container main.site-main article.sticky .archive-post,
    .woocommerce-info,span.view-all a:hover,.vmagazine-post-col.block_layout_1 span.view-all a:hover,
    header.header-layout4 .logo-wrapper-section .vmagazine-container .vmagazine-search-form-primary form.search-form input.search-field:focus,
    .block-post-wrapper.block_layout_3 .view-all a:hover,
    .vmagazine-mul-cat.block-post-wrapper.layout-two .block-content-wrapper .right-posts-wrapper .view-all a:hover,
    .block-post-wrapper.list .gl-posts a.vm-ajax-load-more:hover, .block-post-wrapper.grid-two .gl-posts a.vm-ajax-load-more:hover,
    .vmagazine-cat-slider.block-post-wrapper.block_layout_1 .content-wrapper-featured-slider .lSSlideWrapper li.single-post .post-caption p span.read-more a,
    .no-results.not-found form.search-form input.search-submit,
    .vmagazine-container #primary .entry-content .post-tag .tags-links a,
    .related-content-wrapper a.vmagazine-related-more
    {
        border-color: $vmagazine_theme_color;
    }";

    $custom_css .="
    .vmagazine-container .vmagazine-sidebar .widget.widget_recent_comments ul li span.comment-author-link,
    .vmagazine-container .vmagazine-sidebar .widget.widget_rss ul li a,.woocommerce-page .vmagazine-container.sidebar-shop .widget_recent_reviews ul.product_list_widget li .reviewer,
    .vmagazine-breadcrumb-wrapper .vmagazine-bread-home li.current
    {
        color: $rgba_theme_color;
    }";

    $custom_css .="
    .vmagazine-container .vmagazine-sidebar .widget.widget_search form.search-form input.search-field:hover
    {
        border-color: $clrgba_theme_color;
    }";

    $custom_css .="
    .template-two .widget-title:before, .template-two .block-title:before,
    .template-two .vmagazine-container #primary.vmagazine-content .comment-respond h4.comment-reply-title:before, .template-two .vmagazine-container #primary.vmagazine-content .vmagazine-related-wrapper h4.related-title:before, .template-two .vmagazine-container #primary.vmagazine-content .post-review-wrapper .section-title:before,
    .template-two .vmagazine-container #primary.vmagazine-content .vmagazine-author-metabox h4.box-title::before{
        background: $theme_title_color;
    }";
    $custom_css .="
    .template-three .widget-title span:after, .template-three .block-title span:after,
    .template-three .vmagazine-container #primary.vmagazine-content .post-review-wrapper h4.section-title span:after, .template-three .vmagazine-container #primary.vmagazine-content .vmagazine-related-wrapper h4.related-title span:after, .template-three .vmagazine-container #primary.vmagazine-content .comment-respond h4.comment-reply-title span:after, .template-three .vmagazine-container #primary.vmagazine-content .post-review-wrapper h4.section-title span.title-bg:after,
    .template-three .vmagazine-container #primary.vmagazine-content .vmagazine-author-metabox h4.box-title span.title-bg:after,
    .vmagazine-ticker-wrapper .default-layout .vmagazine-ticker-caption span::before, .vmagazine-ticker-wrapper .layout-two .vmagazine-ticker-caption span::before
    {
        border-color: transparent transparent transparent $vmagazine_theme_color;
    }";
    $custom_css .="
    .vmagazine-rec-posts.recent-post-widget .recent-posts-content .recent-post-content span a:hover{
        color: $rgba_theme_color;
    }";
    
    $custom_css .="
    header.header-layout3 .site-main-nav-wrapper .top-right .vmagazine-search-form-primary{
        border-top: solid 2px $vmagazine_theme_color;
    }";
    $custom_css .="
    .template-four .widget-title span:after, .template-four .block-title span:after, .template-four .vmagazine-container #primary.vmagazine-content .vmagazine-related-wrapper h4.related-title span.title-bg:after, .template-four .comment-respond h4.comment-reply-title span:after, .template-four .vmagazine-container #primary.vmagazine-content .post-review-wrapper h4.section-title span:after
    {
        border-color: $vmagazine_theme_color transparent transparent transparent;
    }";
    
}

   
   
    wp_add_inline_style( 'vmagazine-lite-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'vmagazine_lite_dynamic_css' );