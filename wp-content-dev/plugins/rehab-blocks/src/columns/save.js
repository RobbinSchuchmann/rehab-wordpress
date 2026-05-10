import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { variant, background, verticalAlign } = attributes;

	const blockProps = useBlockProps.save( {
		className: [
			'rehab-columns',
			`rehab-columns--${ variant }`,
			`rehab-bg-${ background }`,
			`rehab-columns--align-${ verticalAlign }`,
		].join( ' ' ),
	} );

	return (
		<section { ...blockProps }>
			<div className="rehab-container">
				<div className="rehab-columns__grid">
					<InnerBlocks.Content />
				</div>
			</div>
		</section>
	);
}
