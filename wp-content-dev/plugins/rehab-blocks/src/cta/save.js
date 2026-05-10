import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const {
		variant,
		background,
		heading,
		body,
		buttonText,
		buttonUrl,
		helper,
	} = attributes;

	const blockProps = useBlockProps.save( {
		className: `rehab-cta rehab-cta--${ variant } rehab-bg-${ background }`,
		'aria-label': 'Call to action',
	} );

	const containerClass = `rehab-container ${
		variant === 'compact' ? 'rehab-container--text' : 'rehab-container--narrow'
	}`;
	const headingClass = `rehab-heading ${
		variant === 'compact' ? 'rehab-heading--md' : 'rehab-heading--lg'
	}`;

	return (
		<section { ...blockProps }>
			<div className={ containerClass }>
				<div className="rehab-cta__inner">
					<RichText.Content
						tagName="h2"
						className={ headingClass }
						value={ heading }
					/>
					{ variant === 'default' && body && (
						<RichText.Content
							tagName="p"
							className="rehab-cta__body"
							value={ body }
						/>
					) }
					{ buttonText && (
						<a
							className="rehab-btn rehab-btn--luxury"
							href={ buttonUrl || '#' }
						>
							<RichText.Content value={ buttonText } />
						</a>
					) }
					{ helper && (
						<RichText.Content
							tagName="p"
							className="rehab-cta__helper"
							value={ helper }
						/>
					) }
				</div>
			</div>
		</section>
	);
}
