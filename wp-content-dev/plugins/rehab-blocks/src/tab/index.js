import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InnerBlocks,
} from '@wordpress/block-editor';
import metadata from './block.json';
import './editor.scss';

const TEMPLATE = [ [ 'core/paragraph', { placeholder: 'Tab content…' } ] ];

function Edit( { attributes, setAttributes } ) {
	const { label } = attributes;
	const blockProps = useBlockProps( { className: 'rehab-tab' } );
	return (
		<div { ...blockProps }>
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
	);
}

function save( { attributes } ) {
	const { label } = attributes;
	const blockProps = useBlockProps.save( {
		className: 'rehab-tab',
		'data-label': label,
	} );
	return (
		<div { ...blockProps }>
			<InnerBlocks.Content />
		</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
