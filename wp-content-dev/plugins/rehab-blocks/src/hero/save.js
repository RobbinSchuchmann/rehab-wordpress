import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const {
		eyebrow,
		headline,
		body,
		buttonText,
		buttonUrl,
		buttonHelper,
		trustItem1,
		trustItem2,
		trustItem3,
		imageUrl,
		imageAlt,
		videoId,
		showDeco,
	} = attributes;

	const blockProps = useBlockProps.save( {
		className: 'rehab-hero',
		'aria-label': 'Hero',
	} );

	const trustItems = [ trustItem1, trustItem2, trustItem3 ].filter( Boolean );

	return (
		<section { ...blockProps }>
			<div className="rehab-hero__container">
				<div className="rehab-hero__grid">
					<div className="rehab-hero__content">
						{ eyebrow && (
							<RichText.Content
								tagName="p"
								className="rehab-hero__eyebrow"
								value={ eyebrow }
							/>
						) }
						<RichText.Content
							tagName="h1"
							className="rehab-hero__h1"
							value={ headline }
						/>
						{ body && (
							<RichText.Content
								tagName="p"
								className="rehab-hero__body"
								value={ body }
							/>
						) }
						{ buttonText && (
							<div className="rehab-hero__cta">
								<a
									className="rehab-btn rehab-btn--luxury"
									href={ buttonUrl || '#' }
								>
									<RichText.Content value={ buttonText } />
								</a>
								{ buttonHelper && (
									<RichText.Content
										tagName="p"
										className="rehab-hero__cta-helper"
										value={ buttonHelper }
									/>
								) }
							</div>
						) }
						{ trustItems.length > 0 && (
							<div className="rehab-hero__trust">
								{ trustItems.map( ( item, idx ) => (
									<div className="rehab-hero__trust-item" key={ idx }>
										<span className="rehab-hero__diamond" aria-hidden="true">◆</span>
										<RichText.Content value={ item } />
									</div>
								) ) }
							</div>
						) }
					</div>

					{ imageUrl && (
						<div className="rehab-hero__media">
							<div
								className="rehab-hero__image-wrap"
								{ ...( videoId ? { 'data-video-id': videoId } : {} ) }
							>
								<img
									src={ imageUrl }
									alt={ imageAlt || '' }
									className="rehab-hero__image"
									loading="eager"
									decoding="async"
								/>
								<div className="rehab-hero__overlay" aria-hidden="true" />
								{ videoId && (
									<div
										className="rehab-hero__play"
										aria-label="Play video tour"
									>
										<svg
											width="28"
											height="28"
											viewBox="0 0 24 24"
											fill="white"
											aria-hidden="true"
										>
											<polygon points="5,3 19,12 5,21" />
										</svg>
									</div>
								) }
							</div>
							{ showDeco && (
								<div className="rehab-hero__deco" aria-hidden="true" />
							) }
						</div>
					) }
				</div>
			</div>
		</section>
	);
}
