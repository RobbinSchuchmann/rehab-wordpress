import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { title, body } = attributes;
	const blockProps = useBlockProps( { className: 'rehab-step' } );
	return (
		<li { ...blockProps }>
			<RichText
				tagName="h3"
				className="rehab-step__title"
				value={ title }
				onChange={ ( v ) => setAttributes( { title: v } ) }
				placeholder={ __( 'Step title', 'rehab-blocks' ) }
				allowedFormats={ [] }
			/>
			<RichText
				tagName="p"
				className="rehab-step__body"
				value={ body }
				onChange={ ( v ) => setAttributes( { body: v } ) }
				placeholder={ __( 'Step body…', 'rehab-blocks' ) }
			/>
		</li>
	);
}

function save( { attributes } ) {
	const { title, body } = attributes;
	const blockProps = useBlockProps.save( { className: 'rehab-step' } );
	return (
		<li { ...blockProps }>
			<RichText.Content
				tagName="h3"
				className="rehab-step__title"
				value={ title }
			/>
			<RichText.Content
				tagName="p"
				className="rehab-step__body"
				value={ body }
			/>
		</li>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
