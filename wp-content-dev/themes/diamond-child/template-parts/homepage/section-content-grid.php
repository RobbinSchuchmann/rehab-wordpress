<?php
/**
 * Homepage Section: Content Grid
 * 25 article cards linking to the blog.
 */

$articles = array(
	array( 'title' => 'Alcohol addiction: definition, signs, and treatment', 'image' => 'alcohol-addiction.webp', 'link' => '/alcohol-addiction/' ),
	array( 'title' => '21 different addiction types: physical, behavioral, and impulse control disorders', 'image' => 'addiction-types.webp', 'link' => '/types-of-addiction/' ),
	array( 'title' => 'Chocolate addiction: symptoms, causes, treatments', 'image' => 'chocolate-addiction.webp', 'link' => '/chocolate-addiction/' ),
	array( 'title' => 'Cocaine addiction: definition, signs, side effects and treatment', 'image' => 'cocaine-addiction.webp', 'link' => '/cocaine-addiction/' ),
	array( 'title' => 'Exercise addiction: definition, symptoms, effects, withdrawal, and treatment', 'image' => 'exercise-addiction.webp', 'link' => '/exercise-addiction/' ),
	array( 'title' => 'Energy drink addiction: prevalence, symptoms, causes, and treatment', 'image' => 'energy-drink-addiction.webp', 'link' => '/energy-drink-addiction/' ),
	array( 'title' => 'Dopamine addiction: can you be addicted, role, and dopamine-seeking behavior', 'image' => 'dopamine-addiction.webp', 'link' => '/dopamine-addiction/' ),
	array( 'title' => 'What is rehab?', 'image' => 'what-is-rehab.webp', 'link' => '/what-is-rehab/' ),
	array( 'title' => 'What is addiction?', 'image' => 'nicotine-addiction.jpg', 'link' => '/what-is-addiction/' ),
	array( 'title' => 'Percocet addiction: definition, symptoms, side effects, withdrawal, and treatments', 'image' => 'percocet-addiction.webp', 'link' => '/percocet-addiction/' ),
	array( 'title' => 'Pornography addiction: signs, causes, effects, and treatment', 'image' => 'pornography-addiction.webp', 'link' => '/pornography-addiction/' ),
	array( 'title' => 'Psychological addiction: symptoms, causes, withdrawal, and treatment', 'image' => 'psychological-addiction.png', 'link' => '/psychological-addiction/' ),
	array( 'title' => 'Relationship addiction: signs, causes, and treatment', 'image' => 'relationship-addiction.webp', 'link' => '/relationship-addiction/' ),
	array( 'title' => 'Facebook addiction: symptoms, causes, negative effects, and treatment', 'image' => 'facebook-addiction.webp', 'link' => '/facebook-addiction/' ),
	array( 'title' => 'Food addiction: definition, symptoms, signs, causes, and treatment', 'image' => 'food-addiction.webp', 'link' => '/food-addiction/' ),
	array( 'title' => 'Heroin addiction: definition, signs, withdrawal, and treatment', 'image' => 'heroin-addiction.webp', 'link' => '/heroin-addiction/' ),
	array( 'title' => 'Hydrocodone addiction: signs, withdrawal symptoms, and treatment', 'image' => 'hydrocodone-addiction.webp', 'link' => '/hydrocodone-addiction/' ),
	array( 'title' => 'Internet addiction: prevalence, types, symptoms, and treatment', 'image' => 'internet-addiction.webp', 'link' => '/internet-addiction/' ),
	array( 'title' => 'Online gambling addiction: signs, symptoms, psychological effects, and treatments', 'image' => 'online-gambling.webp', 'link' => '/online-gambling-addiction/' ),
	array( 'title' => 'Opioid addiction: definition, types, symptoms, signs, long-term effects, withdrawal, and treatment', 'image' => 'opioid-addiction.webp', 'link' => '/opioid-addiction/' ),
	array( 'title' => 'Sex addiction: signs, causes, types, effects, and treatment', 'image' => 'sex-addiction.webp', 'link' => '/sex-addiction/' ),
	array( 'title' => 'Shopping addiction (oniomania): signs, causes, effects, and treatment', 'image' => 'shopping-addiction.webp', 'link' => '/shopping-addiction/' ),
	array( 'title' => 'Social media addiction: prevalence, signs, causes, effects, and treatment', 'image' => 'social-media-addiction.webp', 'link' => '/social-media-addiction/' ),
	array( 'title' => 'Work addiction (workaholism): symptoms, causes, consequences, and treatment', 'image' => 'work-addiction.webp', 'link' => '/work-addiction/' ),
	array( 'title' => 'Xanax (Alprazolam) addiction: symptoms, signs, side effects, and treatment', 'image' => 'xanax-addiction.webp', 'link' => '/xanax-addiction/' ),
);
?>
<section class="drt-content-grid drt-bg-white drt-section--lg" aria-label="Further reading">
	<div class="drt-container drt-container--narrow">
		<div class="drt-section-header">
			<h2 class="drt-heading drt-heading--md">
				Read more addiction, substance abuse treatment and rehab articles
			</h2>
		</div>

		<div class="drt-content-grid__grid">
			<?php foreach ( $articles as $article ) : ?>
				<a href="<?php echo esc_url( $article['link'] ); ?>" class="drt-card--article">
					<div class="drt-card--article__image">
						<img
							src="<?php echo esc_url( drt_homepage_img( 'content-grid/' . $article['image'] ) ); ?>"
							alt="<?php echo esc_attr( $article['title'] ); ?>"
							loading="lazy"
						>
					</div>
					<h3 class="drt-card--article__title"><?php echo esc_html( $article['title'] ); ?></h3>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
