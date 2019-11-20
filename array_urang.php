function data_products() {

    $data       = array();
    // $data[]     = $titles;
    $data[]     = array ( 'CATEGORY' , 'SUBCATEGORY' , 'PRODUCT NAME' , 'DESCRIPTION' , 'SUPPLIER' , 'Stock Control' , 'Option 1 Label' , 'Option 2 Label' , 'Price CODE' , 'Option 1' , 'Option 2' , 'Cost Price', 'Retail Price' , 'Wholesale Price' , 'VIP Price');
    $offset     = 0;
    $block_size = get_option( 'alg_wc_export_wp_query_block_size', 1024 );

    $fields_ids = array ( 'product-parent-cat' , 'product-sub-cat' , 'product-name' , 'product-description' , 'supplayer' , 'stoc' , 'attrlabel1' , 'attrlabel2' , 'price-code' ,'attrvalue1' , 'attrvalue2' , 'costgood' , 'product-price' , 'whoosaleprice', 'supplayer');
    
    // attrvalue2

    while( true ) {
      $args = array(
        'post_type'      => 'product',
        'post_status'    => 'any',
        'posts_per_page' => $block_size,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'offset'         => $offset,
        'fields'         => 'ids',
      );
      $loop = new WP_Query( $args );
      if ( ! $loop->have_posts() ) {
        break;
      }
      foreach ( $loop->posts as $product_id ) {
        $_product = wc_get_product( $product_id );
        $terms = get_the_terms ( $product_id , 'product_cat' );
        $parent_cat = '';
        $sub_cat = '';

        if ( ! empty( $terms ) ) {
          $x = 1;
          foreach ( $terms as $term ) {
              if ( $term->parent == 0 ) {
                $parent_cat = $term->name;
              } else {
                if ( $x == 1) {
                  $sub_cat .= $term->name;
                  $x=2;
                } else {
                  $sub_cat .= $term->name . '-';
                }
              }
          }
        }

        if ( $parent_cat == '') {
          $parent_cat = '-';
        }

        if ( $sub_cat == '') {
          $sub_cat = '-';
        }
        
        $row = array();
        foreach( $fields_ids as $field_id ) {
          switch ( $field_id ) {
            case 'product-id':
              $row[] = $product_id;
              break;
            case 'product-name':
              $row[] = $_product->get_title();
              break;
            case 'product-sku':
              $row[] = $_product->get_sku();
              break;
            case 'product-stock-quantity':
              $row[] = ( $_product->is_type( 'variable' ) || $_product->is_type( 'grouped' ) ?
                $this->get_variable_or_grouped_product_info( $_product, 'stock_quantity' ) : $_product->get_stock_quantity() );
              break;
            case 'product-stock':
              $row[] = ( $_product->is_type( 'variable' ) || $_product->is_type( 'grouped' ) ?
                get_variable_or_grouped_product_info( $_product, 'total_stock' ) : ( $this->is_wc_version_below_3 ? $_product->get_total_stock() : $_product->get_stock_quantity() ) );
              break;
            case 'product-regular-price':
              $row[] = ( $_product->is_type( 'variable' ) || $_product->is_type( 'grouped' ) ?
                get_variable_or_grouped_product_info( $_product, 'regular_price' ) : $_product->get_regular_price() );
              break;
            case 'product-sale-price':
              $row[] = ( $_product->is_type( 'variable' ) || $_product->is_type( 'grouped' ) ?
                get_variable_or_grouped_product_info( $_product, 'sale_price' ) : $_product->get_sale_price() );
              break;
            case 'product-price':
              $row[] = ( $_product->is_type( 'variable' ) || $_product->is_type( 'grouped' ) ?
                get_variable_or_grouped_product_info( $_product, 'price' ) : $_product->get_price() );
              break;
            case 'product-type':
              $row[] = $_product->get_type();
              break;
            case 'product-variation-attributes':
              $row[] = ( $_product->is_type( 'variable' ) ?
                get_variable_or_grouped_product_info( $_product, 'variation_attributes' ) : '' );
              break;
            case 'product-image-url':
              $row[] = alg_get_product_image_url( $product_id, 'full' );
              break;
            case 'product-short-description':
              $row[] = $_product->get_short_description();
              break;
            case 'product-description':
              $row[] = $_product->get_description();
              break;
            case 'product-status':
              $row[] = $_product->get_status();
              break;
            case 'product-url':
              $row[] = $_product->get_permalink();
              break;
            case 'product-shipping-class':
              $row[] = $_product->get_shipping_class();
              break;
            case 'product-shipping-class-id':
              $row[] = $_product->get_shipping_class_id();
              break;
            case 'product-width':
              $row[] = $_product->get_width();
              break;
            case 'product-length':
              $row[] = $_product->get_length();
              break;
            case 'product-height':
              $row[] = $_product->get_height();
              break;
            case 'product-weight':
              $row[] = $_product->get_weight();
              break;
            case 'product-downloadable':
              $row[] = ( $this->is_wc_version_below_3 ? $_product->downloadable : $_product->get_downloadable() );
              break;
            case 'product-virtual':
              $row[] = ( $this->is_wc_version_below_3 ? $_product->virtual : $_product->get_virtual() );
              break;
            case 'product-sold-individually':
              $row[] = ( $this->is_wc_version_below_3 ? $_product->sold_individually : $_product->get_sold_individually() );
              break;
            case 'product-tax-status':
              $row[] = $_product->get_tax_status();
              break;
            case 'product-tax-class':
              $row[] = $_product->get_tax_class();
              break;
            case 'product-manage-stock':
              $row[] = ( $this->is_wc_version_below_3 ? $_product->manage_stock : $_product->get_manage_stock() );
              break;
            case 'product-stock-status':
              $row[] = ( $this->is_wc_version_below_3 ? $_product->stock_status : $_product->get_stock_status() );
              break;
            case 'product-backorders':
              $row[] = ( $this->is_wc_version_below_3 ? $_product->backorders : $_product->get_backorders() );
              break;
            case 'product-featured':
              $row[] = ( $this->is_wc_version_below_3 ? $_product->featured : $_product->get_featured() );
              break;
            case 'product-visibility':
              $row[] = ( $this->is_wc_version_below_3 ? $_product->visibility : $_product->get_catalog_visibility() );
              break;
            case 'product-price-including-tax':
              $row[] = ( $this->is_wc_version_below_3 ? $_product->get_price_including_tax() : wc_get_price_including_tax( $_product ) );
              break;
            case 'product-price-excluding-tax':
              $row[] = ( $this->is_wc_version_below_3 ? $_product->get_price_excluding_tax() : wc_get_price_excluding_tax( $_product ) );
              break;
            case 'product-display-price':
              $row[] = ( $this->is_wc_version_below_3 ? $_product->get_display_price() : wc_get_price_to_display( $_product ) );
              break;
            case 'product-average-rating':
              $row[] = $_product->get_average_rating();
              break;
            case 'product-rating-count':
              $row[] = $_product->get_rating_count();
              break;
            case 'product-review-count':
              $row[] = $_product->get_review_count();
              break;
            case 'product-categories':
              $row[] = strip_tags( ( $this->is_wc_version_below_3 ? $_product->get_categories() : wc_get_product_category_list( $_product->get_id() ) ) );
              break;
            case 'product-tags':
              $row[] = strip_tags( ( $this->is_wc_version_below_3 ? $_product->get_tags() : wc_get_product_tag_list( $_product->get_id() ) ) );
              break;
            case 'product-dimensions':
              $row[] = ( $this->is_wc_version_below_3 ? $_product->get_dimensions() : wc_format_dimensions( $_product->get_dimensions( false ) ) );
              break;
            case 'product-formatted-name':
              $row[] = $_product->get_formatted_name();
              break;
            case 'product-availability':
              $availability = $_product->get_availability();
              $row[] = $availability['availability'];
              break;
            case 'product-availability-class':
              $availability = $_product->get_availability();
              $row[] = $availability['class'];
              break;
            case 'product-parent-cat':
              $row[] = $parent_cat;
              break;
            case 'product-sub-cat':
              $row[] = $sub_cat;
              break;
            case 'supplayer' :
              $row[] = '-';
              break;
            case 'attrlabel1' :
              $row[] = ( $_product->is_type( 'variable' ) ) ? get_variation_value( $product_id , 'attrlabel1' ) : '-' ;
              break;
            case 'attrvalue1' :
              $row[] = ( $_product->is_type( 'variable' ) ) ? get_variation_value( $product_id , 'attrvalue1' ) : '-' ;
              break;
            case 'attrlabel2' :
              $row[] = ( $_product->is_type( 'variable' ) ) ? get_variation_value( $product_id , 'attrlabel2' ) : '-' ;
              break;
            case 'attrvalue2' :
              $row[] = ( $_product->is_type( 'variable' ) ) ? get_variation_value( $product_id , 'attrvalue2' )  : '-' ;
              break;
            case 'price-code' :
              $row[] = '-';
              break;
            case 'stoc' :
              $row[] = 'FIFO';
              break;
            case 'costgood' :
              // $data = get_post_meta( $product_id, 'yith_cog_cost', TRUE );
              // if ( $data == '') {
              //  $data = '-';
              // }
              // $row[] = $data;
              // $row[] = get_post_meta( $product_id, 'yith_cog_cost', TRUE );
              $row[] = '-';
              break;
            case 'whoosaleprice' :
              // $data = get_post_meta( $product_id, 'wholesale_customer_wholesale_price', TRUE );
              // if ( $data == '') {
              //  $data = '-';
              // }
              // $row[] = $data;
              $row[] = '-';
              // $row[] = get_post_meta( $product_id, 'wholesale_customer_wholesale_price', TRUE );
              break;
              
          }
        }

        $data[] = $row;
      }
      $offset += $block_size;
    }
    return $data;
  }
