<?php

/**
 * @var ATD\CruiseFactory\Entity\Factory $atdFactory
 * @var ATD\CruiseFactory\Entity\Departure $atdDeparture
 * @var ATD\CruiseFactory\Entity\Special $atdSpecial
 */
global $atdFactory, $atdDeparture, $atdSpecial;
$cruiseLinePost = atd_cf_get_post_by_meta_value( 'cruise-line', $atdDeparture->getCruise()->getCruiseLine()->getId(), true );
$shipPost       = atd_cf_get_post_by_meta_value( 'ship', $atdDeparture->getCruise()->getShip()->getId(), true );

?>
<h2><?php the_title(); ?></h2>
<div class="atd-cfi__cols" data-controller="atd-cfi-popover">
    <div class="atd-cfi-cols__column">
        <div class="atd-cfi-departure__logo">
            <img class="atd-cfi__img-fluid atd-cfi__mb-2"
                 src="data:<?php echo $atdDeparture->getCruise()->getCruiseLine()->getLogoType(); ?>;base64,<?php echo base64_encode( $atdDeparture->getCruise()->getCruiseLine()->getLogoData() ); ?>"
                 alt="Cruise Line">
            <p><?php echo $atdDeparture->getCruise()->getDuration(); ?> nights onboard
                <a href="<?php echo get_permalink( $shipPost->post->ID ); ?>">
					<?php echo $atdDeparture->getCruise()->getShip()->getName(); ?>
                </a> from
                <a href="<?php echo get_permalink( $cruiseLinePost->post->ID ); ?>">
					<?php echo $atdDeparture->getCruise()->getCruiseLine()->getName(); ?>
                </a> departing <?php echo $atdDeparture->getSailingDate()->format( 'd M Y' ); ?>
            </p>
        </div>
        <div class="atd-cfi-departure__pricing atd-cfi__mt-2" data-controller="atd-cfi-toggle-element"
             data-atd-cfi-toggle-element-prefix-value="atd-cfi-departure-price-">
            <form action="<?php echo get_permalink( get_option( ATD_CF_XML_ENQUIRY_PAGE_ID_FIELD ) ); ?>" method="get">
                <input type="hidden" name="departure_id"
                       value="<?php echo $atdSpecial ? $atdSpecial->getDepartureId() : $atdDeparture->getId(); ?>">
                <input type="hidden" name="departure_type" value="<?php echo $atdSpecial ? 'special' : 'cruise'; ?>">
                <div class="atd-cfi-departure-pricing__prices">
					<?php if ( $atdSpecial ): ?>
                        <h3>Special Pricing</h3>
                        <h4 class="atd-cfi__mb-2">
                            <small>From</small>
                            <small><?php echo $atdSpecial->getCurrency()->getSign(); ?></small><?php echo number_format( $atdSpecial->getStartPrice() ); ?>
                            <small>pp twin share</small>
                        </h4>
						<?php atd_cf_get_template_part( 'content/departure', 'special-pricing' ); ?>
					<?php elseif ( $atdDeparture->getCruisePrices()->count() > 0 ): ?>
                        <h3>Pricing</h3>
                        <h4 class="atd-cfi__mb-2">
                            <small>From</small>
                            <small><?php echo $atdDeparture->getCruisePrices()->get( 0 )->getCurrency(); ?></small><?php echo number_format( $atdDeparture->getCruisePrices()->get( 0 )->getPriceDouble() ); ?>
                            <small>pp twin share</small>
                        </h4>
						<?php atd_cf_get_template_part( 'content/departure', 'cruise-pricing' ); ?>
					<?php else: ?>
                        <h4>Request Price</h4>
						<?php atd_cf_get_template_part( 'content/departure', 'request-pricing' ); ?>
					<?php endif; ?>
                </div>
                <div class="atd-cfi-departure-pricing__buttons">
                    <button class="atd-cfi__btn" type="submit">Continue</button>
                </div>
            </form>
        </div>
    </div>
    <div class="atd-cfi-cols__column atd-cfi-cols-column-2">
        <div class="atd-cfi__tabs" data-controller="atd-cfi-tabs">
            <div class="atd-cfi-tabs__anchors" data-atd-cfi-tabs-target="anchors">
				<?php if ( ! empty( $atdSpecial ) ): ?>
                    <a href="#atd-tab-offer">Offer Details</a>
				<?php endif; ?>
                <a href="#atd-tab-overview">Overview</a>
                <a href="#atd-tab-itinerary">Itinerary</a>
                <a href="#atd-tab-cruise-line">Cruise Line</a>
                <a href="#atd-tab-ship">Ship</a>
                <a href="#atd-tab-cabins">Cabins</a>
                <a href="#atd-tab-decks">Decks</a>
            </div>

            <div class="atd-cfi-tabs__contents" data-atd-cfi-tabs-target="contents">
				<?php if ( ! empty( $atdSpecial ) ): ?>
                    <div id="atd-tab-offer">
                        <p>
                            Valid from
							<?php echo $atdSpecial->getValidFrom()->format( get_option( 'date_format' ) ); ?>
                            until
							<?php echo $atdSpecial->getValidTo()->format( get_option( 'date_format' ) ); ?>
                        </p>
                        <br>
                        <p><?php echo nl2br( $atdSpecial->getInclusions() ); ?></p>
						<?php if ( ! empty( $atdSpecial->getConditions() ) ): ?>
                            <br>
                            <p>
                                <small>
                                    <strong>Terms &amp; Conditions</strong><br>
									<?php echo nl2br( $atdSpecial->getConditions() ); ?>
                                </small>
                            </p>
						<?php endif; ?>
                    </div>
				<?php endif; ?>
                <div id="atd-tab-overview">
					<?php if ( $mapImage = atd_cf_get_media_image_by_meta_key_and_id( 'atd_cfi_cruise_id', $atdDeparture->getCruise()->getId() ) ): ?>
                        <div class="atd-cfi__float-end atd-cfi__ml-2 atd-cfi__mb-2 atd-cfi__mw-40">
                            <a data-action="atd-cfi-popover#image" href="<?php echo $mapImage; ?>">
                                <img class="atd-cfi__img-fluid" src="<?php echo $mapImage; ?>" alt="Map">
                            </a>
                        </div>
					<?php endif; ?>

					<?php $the_content = apply_filters( 'the_content', get_the_content() );
					if ( ! empty( $the_content ) ): ?>
						<?php echo $the_content; ?>
					<?php else: ?>
						<?php echo $atdDeparture->getCruise()->getBriefDescription(); ?>
					<?php endif; ?>
                </div>
                <div id="atd-tab-itinerary"
                     data-controller="atd-cfi-ajax-results"
                     data-atd-cfi-ajax-results-endpoint-value="/wp-json/atd/cfi/v1/<?php echo ! empty( $atdSpecial ) ? 'special-departure' : 'departure'; ?>/<?php echo ! empty( $atdSpecial ) ? $atdSpecial->getDepartureId() : $atdDeparture->getId(); ?>/itinerary">
                    <div data-atd-cfi-ajax-results-target="results">
                        <div class="spinner-loader"></div>
                    </div>
                </div>
                <div id="atd-tab-cruise-line">
					<?php while ( $cruiseLinePost->have_posts() ): $cruiseLinePost->the_post(); ?>
						<?php atd_cf_get_template_part( 'content/cruise-line', 'overview' ); ?>
					<?php endwhile;
					wp_reset_postdata(); ?>
                </div>
				<?php while ( $shipPost->have_posts() ): $shipPost->the_post(); ?>
                    <div id="atd-tab-ship">
						<?php atd_cf_get_template_part( 'content/ship', 'overview' ); ?>
                    </div>
                    <div id="atd-tab-cabins">
						<?php atd_cf_get_template_part( 'content/ship', 'cabins' ); ?>
                    </div>
                    <div id="atd-tab-decks">
						<?php atd_cf_get_template_part( 'content/ship', 'decks' ); ?>
                    </div>
				<?php endwhile;
				wp_reset_postdata(); ?>
            </div>
        </div>
    </div>
</div>