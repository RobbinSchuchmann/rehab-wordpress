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
	[ 'rehab/feature' ],
	[ 'rehab/feature' ],
	[ 'rehab/feature' ],
];
const ALLOWED = [ 'rehab/feature' ];

function Edit( { attributes, setAttributes } ) {
	const { background, columns, heading, subheading } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-features rehab-bg-${ background } rehab-features--cols-${ columns }`,
	} );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Features settings', 'rehab-blocks' ) } initialOpen>
					<SelectControl
						label={ __( 'Columns (desktop)', 'rehab-blocks' ) }
						value={ String( columns ) }
						options={ [
							{ label: '2', value: '2' },
							{ label: '3', value: '3' },
							{ label: '4', value: '4' },
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
					<header className="rehab-features__header">
						<RichText
							tagName="h2"
							className="rehab-heading rehab-heading--lg"
							value={ heading }
							onChange={ ( v ) => setAttributes( { heading: v } ) }
							placeholder={ __( 'Optional heading…', 'rehab-blocks' ) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>
						<RichText
							tagName="p"
							className="rehab-features__subheading"
							value={ subheading }
							onChange={ ( v ) => setAttributes( { subheading: v } ) }
							placeholder={ __( 'Optional subheading…', 'rehab-blocks' ) }
						/>
					</header>
					<div className="rehab-features__grid">
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
		className: `rehab-features rehab-bg-${ background } rehab-features--cols-${ columns }`,
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container">
				{ ( heading || subheading ) && (
					<header className="rehab-features__header">
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
								className="rehab-features__subheading"
								value={ subheading }
							/>
						) }
					</header>
				) }
				<div className="rehab-features__grid">
					<InnerBlocks.Content />
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
