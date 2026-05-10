import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

const TEMPLATE = [
	[ 'rehab/testimonial' ],
	[ 'rehab/testimonial' ],
	[ 'rehab/testimonial' ],
];
const ALLOWED = [ 'rehab/testimonial' ];

function Edit( { attributes, setAttributes } ) {
	const { background, columns, heading, subheading } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-testimonials rehab-bg-${ background } rehab-testimonials--cols-${ columns }`,
	} );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Testimonials settings', 'rehab-blocks' ) } initialOpen>
					<SelectControl
						label={ __( 'Columns (desktop)', 'rehab-blocks' ) }
						value={ String( columns ) }
						options={ [
							{ label: '1 column', value: '1' },
							{ label: '2 columns', value: '2' },
							{ label: '3 columns', value: '3' },
						] }
						onChange={ ( v ) => setAttributes( { columns: parseInt( v, 10 ) } ) }
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
					<header className="rehab-testimonials__header">
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
							className="rehab-testimonials__subheading"
							value={ subheading }
							onChange={ ( v ) => setAttributes( { subheading: v } ) }
							placeholder={ __( 'Subheading…', 'rehab-blocks' ) }
						/>
					</header>
					<div className="rehab-testimonials__grid">
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

function save( { attributes } ) {
	const { background, columns, heading, subheading } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-testimonials rehab-bg-${ background } rehab-testimonials--cols-${ columns }`,
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container">
				{ ( heading || subheading ) && (
					<header className="rehab-testimonials__header">
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
								className="rehab-testimonials__subheading"
								value={ subheading }
							/>
						) }
					</header>
				) }
				<div className="rehab-testimonials__grid">
					<InnerBlocks.Content />
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
