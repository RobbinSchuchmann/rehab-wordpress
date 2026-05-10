import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	URLInput,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { duration, title, price, priceSuffix, body, ctaText, ctaUrl } = attributes;
	const blockProps = useBlockProps( { className: 'rehab-program' } );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Program CTA', 'rehab-blocks' ) } initialOpen>
					<p style={ { marginTop: 0, fontSize: '12px', opacity: 0.7 } }>
						{ __( 'Button URL', 'rehab-blocks' ) }
					</p>
					<URLInput
						value={ ctaUrl }
						onChange={ ( v ) => setAttributes( { ctaUrl: v } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<RichText
					tagName="span"
					className="rehab-program__duration"
					value={ duration }
					onChange={ ( v ) => setAttributes( { duration: v } ) }
					placeholder={ __( 'Duration', 'rehab-blocks' ) }
					allowedFormats={ [] }
				/>
				<RichText
					tagName="h3"
					className="rehab-program__title"
					value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) }
					placeholder={ __( 'Program title', 'rehab-blocks' ) }
					allowedFormats={ [] }
				/>
				<div>
					<RichText
						tagName="span"
						className="rehab-program__price"
						value={ price }
						onChange={ ( v ) => setAttributes( { price: v } ) }
						placeholder="$0"
						allowedFormats={ [] }
					/>
					<RichText
						tagName="span"
						className="rehab-program__price-suffix"
						value={ priceSuffix }
						onChange={ ( v ) => setAttributes( { priceSuffix: v } ) }
						placeholder={ __( 'per month', 'rehab-blocks' ) }
						allowedFormats={ [] }
					/>
				</div>
				<RichText
					tagName="p"
					className="rehab-program__body"
					value={ body }
					onChange={ ( v ) => setAttributes( { body: v } ) }
					placeholder={ __( 'What this program includes…', 'rehab-blocks' ) }
				/>
				<RichText
					tagName="span"
					className="rehab-btn rehab-btn--outline rehab-program__cta"
					value={ ctaText }
					onChange={ ( v ) => setAttributes( { ctaText: v } ) }
					placeholder={ __( 'Button text', 'rehab-blocks' ) }
					allowedFormats={ [] }
				/>
			</div>
		</>
	);
}

function save( { attributes } ) {
	const { duration, title, price, priceSuffix, body, ctaText, ctaUrl } = attributes;
	const blockProps = useBlockProps.save( { className: 'rehab-program' } );
	return (
		<div { ...blockProps }>
			{ duration && (
				<RichText.Content
					tagName="span"
					className="rehab-program__duration"
					value={ duration }
				/>
			) }
			{ title && (
				<RichText.Content
					tagName="h3"
					className="rehab-program__title"
					value={ title }
				/>
			) }
			{ ( price || priceSuffix ) && (
				<div>
					{ price && (
						<RichText.Content
							tagName="span"
							className="rehab-program__price"
							value={ price }
						/>
					) }
					{ priceSuffix && (
						<RichText.Content
							tagName="span"
							className="rehab-program__price-suffix"
							value={ priceSuffix }
						/>
					) }
				</div>
			) }
			{ body && (
				<RichText.Content
					tagName="p"
					className="rehab-program__body"
					value={ body }
				/>
			) }
			{ ctaText && (
				<a
					className="rehab-btn rehab-btn--outline rehab-program__cta"
					href={ ctaUrl || '#' }
				>
					<RichText.Content value={ ctaText } />
				</a>
			) }
		</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
