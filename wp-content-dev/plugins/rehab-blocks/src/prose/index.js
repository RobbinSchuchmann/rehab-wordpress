import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

const ALLOWED_BLOCKS = [
	'core/paragraph',
	'core/heading',
	'core/list',
	'core/list-item',
	'core/quote',
	'core/image',
	'core/embed',
	'core/buttons',
	'core/button',
	'core/separator',
	'core/table',
];

const TEMPLATE = [
	[ 'core/heading', { level: 2, content: 'Section heading' } ],
	[ 'core/paragraph', { content: 'Drop your article content here. This block uses the rehab typography system: serif Ivymode for headings, Inter for body, with comfortable line height and reading width.' } ],
];

function Edit( { attributes, setAttributes } ) {
	const { background, width } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-prose rehab-bg-${ background } rehab-prose--${ width }`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Prose settings', 'rehab-blocks' ) } initialOpen>
					<SelectControl
						label={ __( 'Width', 'rehab-blocks' ) }
						value={ width }
						options={ [
							{ label: 'Reading width (recommended)', value: 'text' },
							{ label: 'Narrow', value: 'narrow' },
							{ label: 'Default container', value: 'default' },
						] }
						onChange={ ( v ) => setAttributes( { width: v } ) }
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
				<div className={ `rehab-container rehab-container--${ width === 'default' ? 'narrow' : width }` }>
					<div className="rehab-prose__inner">
						<InnerBlocks
							template={ TEMPLATE }
							allowedBlocks={ ALLOWED_BLOCKS }
						/>
					</div>
				</div>
			</section>
		</>
	);
}

function save( { attributes } ) {
	const { background, width } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-prose rehab-bg-${ background } rehab-prose--${ width }`,
	} );
	return (
		<section { ...blockProps }>
			<div className={ `rehab-container rehab-container--${ width === 'default' ? 'narrow' : width }` }>
				<div className="rehab-prose__inner">
					<InnerBlocks.Content />
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
