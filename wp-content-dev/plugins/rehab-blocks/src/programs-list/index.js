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
	[ 'rehab/program' ],
	[ 'rehab/program' ],
	[ 'rehab/program' ],
];
const ALLOWED = [ 'rehab/program' ];

function Edit( { attributes, setAttributes } ) {
	const { background, heading, subheading } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-programs rehab-bg-${ background }`,
	} );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Programs settings', 'rehab-blocks' ) } initialOpen>
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
					<header className="rehab-programs__header">
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
							className="rehab-programs__subheading"
							value={ subheading }
							onChange={ ( v ) => setAttributes( { subheading: v } ) }
							placeholder={ __( 'Subheading…', 'rehab-blocks' ) }
						/>
					</header>
					<div className="rehab-programs__list">
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
	const { background, heading, subheading } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-programs rehab-bg-${ background }`,
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container">
				{ ( heading || subheading ) && (
					<header className="rehab-programs__header">
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
								className="rehab-programs__subheading"
								value={ subheading }
							/>
						) }
					</header>
				) }
				<div className="rehab-programs__list">
					<InnerBlocks.Content />
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
