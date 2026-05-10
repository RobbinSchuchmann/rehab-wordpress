import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
	URLInput,
} from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	TextControl,
	Placeholder,
} from '@wordpress/components';
import { image as imageIcon } from '@wordpress/icons';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { imageUrl, imageId, imageAlt, title, description, url } = attributes;
	const blockProps = useBlockProps( { className: 'rehab-card' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Card link', 'rehab-blocks' ) } initialOpen>
					<p style={ { marginTop: 0, fontSize: '12px', opacity: 0.7 } }>
						{ __( 'Card URL (optional)', 'rehab-blocks' ) }
					</p>
					<URLInput
						value={ url }
						onChange={ ( v ) => setAttributes( { url: v } ) }
					/>
					<TextControl
						label={ __( 'Image alt text', 'rehab-blocks' ) }
						value={ imageAlt }
						onChange={ ( v ) => setAttributes( { imageAlt: v } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<div className="rehab-card__image">
					{ imageUrl ? (
						<img src={ imageUrl } alt={ imageAlt || '' } />
					) : (
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( media ) =>
									setAttributes( {
										imageUrl: media.url,
										imageId: media.id,
										imageAlt: media.alt || '',
									} )
								}
								allowedTypes={ [ 'image' ] }
								value={ imageId }
								render={ ( { open } ) => (
									<Placeholder icon={ imageIcon } label={ __( 'Card image', 'rehab-blocks' ) }>
										<Button variant="primary" onClick={ open }>
											{ __( 'Choose', 'rehab-blocks' ) }
										</Button>
									</Placeholder>
								) }
							/>
						</MediaUploadCheck>
					) }
				</div>
				<div className="rehab-card__body">
					<RichText
						tagName="h3"
						className="rehab-card__title"
						value={ title }
						onChange={ ( v ) => setAttributes( { title: v } ) }
						placeholder={ __( 'Card title', 'rehab-blocks' ) }
						allowedFormats={ [] }
					/>
					<RichText
						tagName="p"
						className="rehab-card__description"
						value={ description }
						onChange={ ( v ) => setAttributes( { description: v } ) }
						placeholder={ __( 'Card description…', 'rehab-blocks' ) }
					/>
				</div>
			</div>
		</>
	);
}

function save( { attributes } ) {
	const { imageUrl, imageAlt, title, description, url } = attributes;
	const blockProps = useBlockProps.save( { className: 'rehab-card' } );

	const inner = (
		<>
			{ imageUrl && (
				<div className="rehab-card__image">
					<img
						src={ imageUrl }
						alt={ imageAlt || '' }
						loading="lazy"
					/>
				</div>
			) }
			<div className="rehab-card__body">
				<RichText.Content
					tagName="h3"
					className="rehab-card__title"
					value={ title }
				/>
				<RichText.Content
					tagName="p"
					className="rehab-card__description"
					value={ description }
				/>
			</div>
		</>
	);

	return url ? (
		<a { ...blockProps } href={ url }>{ inner }</a>
	) : (
		<div { ...blockProps }>{ inner }</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
