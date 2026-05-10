import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { question, answer } = attributes;
	const blockProps = useBlockProps( { className: 'rehab-faq-item is-editor' } );

	return (
		<details { ...blockProps } open>
			<summary>
				<RichText
					tagName="span"
					className="rehab-faq-item__question"
					value={ question }
					onChange={ ( v ) => setAttributes( { question: v } ) }
					placeholder={ __( 'Question', 'rehab-blocks' ) }
					allowedFormats={ [] }
				/>
			</summary>
			<RichText
				tagName="div"
				className="rehab-faq-item__answer"
				value={ answer }
				onChange={ ( v ) => setAttributes( { answer: v } ) }
				placeholder={ __( 'Answer', 'rehab-blocks' ) }
				allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
				multiline="p"
			/>
		</details>
	);
}

function save( { attributes } ) {
	const { question, answer } = attributes;
	const blockProps = useBlockProps.save( { className: 'rehab-faq-item' } );
	return (
		<details { ...blockProps }>
			<summary>
				<RichText.Content value={ question } />
			</summary>
			<RichText.Content
				tagName="div"
				className="rehab-faq-item__answer"
				value={ answer }
			/>
		</details>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
