import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
} from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, Placeholder } from '@wordpress/components';
import { image as imageIcon } from '@wordpress/icons';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

function Edit( { attributes, setAttributes } ) {
	const { background, imageUrl, imageId, imageAlt, quote, body, name, role } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-founder rehab-bg-${ background }`,
	} );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Founder bio', 'rehab-blocks' ) } initialOpen>
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
					<TextControl
						label={ __( 'Image alt text', 'rehab-blocks' ) }
						value={ imageAlt }
						onChange={ ( v ) => setAttributes( { imageAlt: v } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="rehab-container">
					<div className="rehab-founder__grid">
						<div className="rehab-founder__media">
							{ imageUrl ? (
								<img src={ imageUrl } alt={ imageAlt || name } className="rehab-founder__photo" />
							) : (
								<MediaUploadCheck>
									<MediaUpload
										onSelect={ ( m ) =>
											setAttributes( {
												imageUrl: m.url,
												imageId: m.id,
												imageAlt: m.alt || '',
											} )
										}
										allowedTypes={ [ 'image' ] }
										value={ imageId }
										render={ ( { open } ) => (
											<Placeholder icon={ imageIcon } label={ __( 'Founder portrait', 'rehab-blocks' ) }>
												<Button variant="primary" onClick={ open }>
													{ __( 'Choose image', 'rehab-blocks' ) }
												</Button>
											</Placeholder>
										) }
									/>
								</MediaUploadCheck>
							) }
						</div>
						<div className="rehab-founder__content">
							<RichText
								tagName="blockquote"
								className="rehab-founder__quote"
								value={ quote }
								onChange={ ( v ) => setAttributes( { quote: v } ) }
								placeholder={ __( 'Pull-quote…', 'rehab-blocks' ) }
								allowedFormats={ [ 'core/bold', 'core/italic' ] }
							/>
							<RichText
								tagName="p"
								className="rehab-founder__body"
								value={ body }
								onChange={ ( v ) => setAttributes( { body: v } ) }
								placeholder={ __( 'Body / context…', 'rehab-blocks' ) }
							/>
							<div className="rehab-founder__signature">
								<RichText
									tagName="p"
									className="rehab-founder__name"
									value={ name }
									onChange={ ( v ) => setAttributes( { name: v } ) }
									placeholder={ __( 'Name', 'rehab-blocks' ) }
									allowedFormats={ [] }
								/>
								<RichText
									tagName="p"
									className="rehab-founder__role"
									value={ role }
									onChange={ ( v ) => setAttributes( { role: v } ) }
									placeholder={ __( 'Role', 'rehab-blocks' ) }
									allowedFormats={ [] }
								/>
							</div>
						</div>
					</div>
				</div>
			</section>
		</>
	);
}

function save( { attributes } ) {
	const { background, imageUrl, imageAlt, quote, body, name, role } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-founder rehab-bg-${ background }`,
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container">
				<div className="rehab-founder__grid">
					{ imageUrl && (
						<div className="rehab-founder__media">
							<img
								src={ imageUrl }
								alt={ imageAlt || name }
								className="rehab-founder__photo"
								loading="lazy"
							/>
						</div>
					) }
					<div className="rehab-founder__content">
						<RichText.Content
							tagName="blockquote"
							className="rehab-founder__quote"
							value={ quote }
						/>
						<RichText.Content
							tagName="p"
							className="rehab-founder__body"
							value={ body }
						/>
						<div className="rehab-founder__signature">
							<RichText.Content
								tagName="p"
								className="rehab-founder__name"
								value={ name }
							/>
							<RichText.Content
								tagName="p"
								className="rehab-founder__role"
								value={ role }
							/>
						</div>
					</div>
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
