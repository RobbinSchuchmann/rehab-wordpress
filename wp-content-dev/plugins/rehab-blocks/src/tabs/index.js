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
	[ 'rehab/tab', { label: 'First tab' } ],
	[ 'rehab/tab', { label: 'Second tab' } ],
	[ 'rehab/tab', { label: 'Third tab' } ],
];
const ALLOWED = [ 'rehab/tab' ];

function Edit( { attributes, setAttributes } ) {
	const { background, heading } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-tabs rehab-bg-${ background }`,
	} );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Tabs settings', 'rehab-blocks' ) } initialOpen>
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
					{ ( heading || true ) && (
						<RichText
							tagName="h2"
							className="rehab-heading rehab-heading--lg rehab-tabs__heading"
							value={ heading }
							onChange={ ( v ) => setAttributes( { heading: v } ) }
							placeholder={ __( 'Optional heading…', 'rehab-blocks' ) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>
					) }
					<div className="rehab-tabs__inner">
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
	const { background, heading } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-tabs rehab-bg-${ background }`,
		'data-rehab-tabs': '',
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container">
				{ heading && (
					<RichText.Content
						tagName="h2"
						className="rehab-heading rehab-heading--lg rehab-tabs__heading"
						value={ heading }
					/>
				) }
				<div className="rehab-tabs__inner">
					<InnerBlocks.Content />
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
