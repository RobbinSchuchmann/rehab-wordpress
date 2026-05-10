import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';
import './editor.scss';

const TEMPLATE = [ [ 'core/paragraph', { placeholder: 'Tab content…' } ] ];

function Edit( { attributes, setAttributes } ) {
	const { label, phaseNumber } = attributes;
	const blockProps = useBlockProps( { className: 'rehab-tab' } );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Tab settings', 'rehab-blocks' ) } initialOpen>
					<TextControl
						label={ __( 'Phase number / eyebrow', 'rehab-blocks' ) }
						value={ phaseNumber }
						onChange={ ( v ) => setAttributes( { phaseNumber: v } ) }
						help={ __( 'Optional small caps eyebrow, e.g. "PHASE 01"', 'rehab-blocks' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ phaseNumber && (
					<div className="rehab-tab__phase-edit">{ phaseNumber }</div>
				) }
				<RichText
					tagName="div"
					className="rehab-tab__label-edit"
					value={ label }
					onChange={ ( v ) => setAttributes( { label: v } ) }
					placeholder={ __( 'Tab label', 'rehab-blocks' ) }
					allowedFormats={ [] }
				/>
				<div className="rehab-tab__panel">
					<InnerBlocks template={ TEMPLATE } />
				</div>
			</div>
		</>
	);
}

function save( { attributes } ) {
	const { label, phaseNumber } = attributes;
	const blockProps = useBlockProps.save( {
		className: 'rehab-tab',
		'data-label': label,
		'data-phase': phaseNumber || '',
	} );
	return (
		<div { ...blockProps }>
			<InnerBlocks.Content />
		</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
