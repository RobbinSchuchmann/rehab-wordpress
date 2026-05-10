import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

// Two locked column children inside this parent.
const TEMPLATE = [
	[ 'rehab/column' ],
	[ 'rehab/column' ],
];
const ALLOWED = [ 'rehab/column' ];

export default function Edit( { attributes, setAttributes } ) {
	const { variant, background, verticalAlign } = attributes;

	const blockProps = useBlockProps( {
		className: [
			'rehab-columns',
			`rehab-columns--${ variant }`,
			`rehab-bg-${ background }`,
			`rehab-columns--align-${ verticalAlign }`,
		].join( ' ' ),
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Columns settings', 'rehab-blocks' ) } initialOpen>
					<SelectControl
						label={ __( 'Variant', 'rehab-blocks' ) }
						value={ variant }
						options={ [
							{ label: 'Default', value: 'default' },
							{ label: 'Divided (line between columns)', value: 'divided' },
						] }
						onChange={ ( v ) => setAttributes( { variant: v } ) }
					/>
					<SelectControl
						label={ __( 'Background', 'rehab-blocks' ) }
						value={ background }
						options={ [
							{ label: 'White', value: 'white' },
							{ label: 'Cream', value: 'cream' },
							{ label: 'Sage mist', value: 'sage-mist' },
						] }
						onChange={ ( v ) => setAttributes( { background: v } ) }
					/>
					<SelectControl
						label={ __( 'Vertical alignment', 'rehab-blocks' ) }
						value={ verticalAlign }
						options={ [
							{ label: 'Top', value: 'top' },
							{ label: 'Center', value: 'center' },
							{ label: 'Bottom', value: 'bottom' },
						] }
						onChange={ ( v ) => setAttributes( { verticalAlign: v } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				<div className="rehab-container">
					<div className="rehab-columns__grid">
						<InnerBlocks
							template={ TEMPLATE }
							templateLock="all"
							allowedBlocks={ ALLOWED }
						/>
					</div>
				</div>
			</section>
		</>
	);
}
