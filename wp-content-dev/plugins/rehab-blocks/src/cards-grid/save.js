import { useBlockProps, RichText, InnerBlocks } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { background, columns, cardLayout, heading, subheading } = attributes;

	const blockProps = useBlockProps.save( {
		className: [
			'rehab-cards-grid',
			`rehab-bg-${ background }`,
			`rehab-cards-grid--cols-${ columns }`,
			`rehab-cards-grid--card-${ cardLayout }`,
		].join( ' ' ),
	} );

	return (
		<section { ...blockProps }>
			<div className="rehab-container">
				{ ( heading || subheading ) && (
					<header className="rehab-cards-grid__header">
						{ heading && (
							<RichText.Content
								tagName="h2"
								className="rehab-heading rehab-heading--lg"
								value={ heading }
							/>
						) }
						{ subheading && (
							<RichText.Content
								tagName="p"
								className="rehab-cards-grid__subheading"
								value={ subheading }
							/>
						) }
					</header>
				) }
				<div className="rehab-cards-grid__grid">
					<InnerBlocks.Content />
				</div>
			</div>
		</section>
	);
}
