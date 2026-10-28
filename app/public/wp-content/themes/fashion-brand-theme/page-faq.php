<?php
/**
 * Template Name: FAQ Page
 * FAQ page template.
 *
 * Used automatically for a Page with slug "faq".
 *
 * @package Fashion_Brand_Theme
 */

get_header();

// Editable hero copy — change these strings to update the page wording.
$hero_eyebrow = __( 'Help', 'fashion-brand-theme' );
$hero_heading = __( 'Frequently asked questions', 'fashion-brand-theme' );

$intro = __(
	'Answers to the questions we hear most often. If you need something more specific, write to us at hello@wrenwold.com — we usually reply within one business day.',
	'fashion-brand-theme'
);

/**
 * FAQ groups: category label + list of question/answer pairs.
 * Edit this array directly to update the page content.
 *
 * @var array<int, array{title: string, items: array<int, array{q: string, a: string}>}>
 */
$faq_groups = array(
	array(
		'title' => __( 'Shipping & delivery', 'fashion-brand-theme' ),
		'items' => array(
			array(
				'q' => __( 'Where do you ship?', 'fashion-brand-theme' ),
				'a' => __( 'We currently ship within the European Union. Delivery options and estimated times are shown at checkout once you enter your address. International destinations outside the EU may be added later — please check this page or contact us if you are unsure.', 'fashion-brand-theme' ),
			),
			array(
				'q' => __( 'How long will my order take to arrive?', 'fashion-brand-theme' ),
				'a' => __( 'Most EU orders leave our warehouse within 1–2 business days and arrive in 2–5 business days after dispatch, depending on your country and the carrier. You will receive a confirmation email when your order is placed and a second email with tracking once it ships.', 'fashion-brand-theme' ),
			),
			array(
				'q' => __( 'Do you offer free shipping?', 'fashion-brand-theme' ),
				'a' => __( 'Free standard shipping applies to qualifying orders above the threshold shown at checkout. Below that amount, shipping is calculated based on your destination and preferred delivery speed. Exact costs always appear before you pay.', 'fashion-brand-theme' ),
			),
		),
	),
	array(
		'title' => __( 'Returns & exchanges', 'fashion-brand-theme' ),
		'items' => array(
			array(
				'q' => __( 'What is your returns policy?', 'fashion-brand-theme' ),
				'a' => __( 'Unworn items in original condition with tags attached can usually be returned within 30 days of delivery. Please see our Shipping & Returns (Refund and Returns Policy) page for the full terms, including any exclusions such as final-sale pieces.', 'fashion-brand-theme' ),
			),
			array(
				'q' => __( 'How do I start a return or exchange?', 'fashion-brand-theme' ),
				'a' => __( 'Sign in to your account, open the order, and follow the return instructions — or email hello@wrenwold.com with your order number and the items you wish to return. We will send a prepaid label or return address where available for your region.', 'fashion-brand-theme' ),
			),
			array(
				'q' => __( 'When will I receive my refund?', 'fashion-brand-theme' ),
				'a' => __( 'Once we receive and inspect your return, refunds are typically processed within 5–10 business days to the original payment method. Bank timelines can add a few extra days before the amount appears on your statement.', 'fashion-brand-theme' ),
			),
		),
	),
	array(
		'title' => __( 'Sizing', 'fashion-brand-theme' ),
		'items' => array(
			array(
				'q' => __( 'How do I find my size?', 'fashion-brand-theme' ),
				'a' => __( 'Each product page includes a size guide with garment measurements. Compare those to a similar piece you already own, measuring flat where possible. If you are between sizes, we generally recommend sizing up for a more relaxed fit and sizing down for a closer one — notes on fit are also listed in the product description.', 'fashion-brand-theme' ),
			),
			array(
				'q' => __( 'Do your sizes run true to standard EU sizing?', 'fashion-brand-theme' ),
				'a' => __( 'Our sizes follow common EU women’s sizing, but cuts vary by style. Always check the size chart on the product page rather than relying on a size you wear elsewhere. If something arrives and the fit is not right, our returns window covers unused items.', 'fashion-brand-theme' ),
			),
		),
	),
	array(
		'title' => __( 'Orders & tracking', 'fashion-brand-theme' ),
		'items' => array(
			array(
				'q' => __( 'How can I track my order?', 'fashion-brand-theme' ),
				'a' => __( 'When your order ships, we email a tracking link from the carrier. You can also find order status under My Account → Orders after signing in with the email used at checkout.', 'fashion-brand-theme' ),
			),
			array(
				'q' => __( 'Can I change or cancel an order?', 'fashion-brand-theme' ),
				'a' => __( 'If your order has not yet been dispatched, email hello@wrenwold.com as soon as possible with your order number. Once it is with the carrier we cannot change the address or contents, but you can refuse delivery or return the parcel under our returns policy.', 'fashion-brand-theme' ),
			),
		),
	),
	array(
		'title' => __( 'Payment', 'fashion-brand-theme' ),
		'items' => array(
			array(
				'q' => __( 'Which payment methods do you accept?', 'fashion-brand-theme' ),
				'a' => __( 'We accept major credit and debit cards and other methods shown at checkout (for example ideal, Bancontact, or Apple Pay where available in your region). All payments are processed securely through our payment provider — we never store full card details on our servers.', 'fashion-brand-theme' ),
			),
			array(
				'q' => __( 'Will I be charged in my local currency?', 'fashion-brand-theme' ),
				'a' => __( 'Prices are displayed in the store currency shown on the site. Your bank or card issuer may convert the charge and may apply their own exchange fees. The amount authorised at checkout is what we charge.', 'fashion-brand-theme' ),
			),
		),
	),
	array(
		'title' => __( 'Account', 'fashion-brand-theme' ),
		'items' => array(
			array(
				'q' => __( 'Do I need an account to shop?', 'fashion-brand-theme' ),
				'a' => __( 'You can check out as a guest. Creating an account lets you track orders, save a wishlist, manage addresses, and update your details in one place. You can register at any time from the Account page.', 'fashion-brand-theme' ),
			),
			array(
				'q' => __( 'I forgot my password — how do I reset it?', 'fashion-brand-theme' ),
				'a' => __( 'On the sign-in screen, choose “Lost your password?”, enter the email linked to your account, and follow the reset link we send. If you do not see the email, check spam or contact hello@wrenwold.com for help.', 'fashion-brand-theme' ),
			),
		),
	),
);
?>

<main id="primary" class="site-main faq-page">
	<section class="faq-hero-banner faq-hero-banner--toned" aria-labelledby="faq-hero-heading">
		<div class="faq-hero-banner__tone" aria-hidden="true"></div>
		<div class="faq-hero-banner__overlay">
			<p class="faq-hero-banner__eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
			<h1 id="faq-hero-heading" class="faq-hero-banner__heading">
				<?php echo esc_html( $hero_heading ); ?>
			</h1>
		</div>
	</section>

	<section class="faq-page__content" aria-label="<?php esc_attr_e( 'Frequently asked questions', 'fashion-brand-theme' ); ?>">
		<div class="faq-page__inner">
			<p class="faq-page__intro"><?php echo esc_html( $intro ); ?></p>

			<?php foreach ( $faq_groups as $group_index => $group ) : ?>
				<section class="faq-group" aria-labelledby="faq-group-<?php echo esc_attr( (string) $group_index ); ?>">
					<h2 id="faq-group-<?php echo esc_attr( (string) $group_index ); ?>" class="faq-group__title">
						<?php echo esc_html( $group['title'] ); ?>
					</h2>
					<div class="faq-accordion">
						<?php foreach ( $group['items'] as $item ) : ?>
							<details class="faq-item">
								<summary class="faq-item__question">
									<span class="faq-item__question-text"><?php echo esc_html( $item['q'] ); ?></span>
									<span class="faq-item__icon" aria-hidden="true"></span>
								</summary>
								<div class="faq-item__answer">
									<p><?php echo esc_html( $item['a'] ); ?></p>
								</div>
							</details>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endforeach; ?>

			<p class="faq-page__footer-note">
				<?php esc_html_e( 'Still need help? Reach us at', 'fashion-brand-theme' ); ?>
				<a href="mailto:hello@wrenwold.com">hello@wrenwold.com</a>.
			</p>
		</div>
	</section>
</main>

<?php
get_footer();
