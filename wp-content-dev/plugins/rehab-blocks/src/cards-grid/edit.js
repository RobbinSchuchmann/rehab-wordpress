import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

const TEMPLATE = [
	[ 'rehab/card' ],
	[ 'rehab/card' ],
	[ 'rehab/card' ],
];
const ALLOWED = [ 'rehab/card' ];

export default function Edit( { attributes, setAttributes } ) {
	const { background, columns, cardLayout, heading, subheading } = attributes;

	const blockProps = useBlockProps( {
		className: [
			'rehab-cards-grid',
			`rehab-bg-${ background }`,
			`rehab-cards-grid--cols-${ columns }`,
			`rehab-cards-grid--card-${ cardLayout }`,
		].join( ' ' ),
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Grid settings', 'rehab-blocks' ) } initialOpen>
					<SelectControl
						label={ __( 'Columns (desktop)', 'rehab-blocks' ) }
						value={ String( columns ) }
						options={ [
							{ label: '2 columns', value: '2' },
							{ label: '3 columns', value: '3' },
							{ label: '4 columns', value: '4' },
						] }
						onChange={ ( v ) => setAttributes( { columns: parseInt( v, 10 ) } ) }
					/>
					<SelectControl
						label={ __( 'Card layout', 'rehab-blocks' ) }
						value={ cardLayout }
						options={ [
							{ label: 'Horizontal (image | text)', value: 'horizontal' },
							{ label: 'Vertical (image on top)', value: 'vertical' },
						] }
						onChange={ ( v ) => setAttributes( { cardLayout: v } ) }
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
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				<div className="rehab-container">
					<header className="rehab-cards-grid__header">
						<RichText
							tagName="h2"
							className="rehab-heading rehab-heading--lg"
							value={ heading }
							onChange={ ( v ) => setAttributes( { heading: v } ) }
							placeholder={ __( 'Section heading…', 'rehab-blocks' ) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>
						<RichText
							tagName="p"
							className="rehab-cards-grid__subheading"
							value={ subheading }
							onChange={ ( v ) => setAttributes( { subheading: v } ) }
							placeholder={ __( 'Optional subheading…', 'rehab-blocks' ) }
						/>
					</header>
					<div className="rehab-cards-grid__grid">
						<InnerBlocks
							template={ TEMPLATE }
							allowedBlocks={ ALLOWED }
							renderAppender={ InnerBlocks.ButtonBlockAppender }
						/>
					</div>
				</div>
			</section>
		</>
	);
}
