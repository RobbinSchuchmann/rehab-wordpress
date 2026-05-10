import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
} from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { icon, iconImage, title, body } = attributes;
	const blockProps = useBlockProps( { className: 'rehab-feature' } );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Feature icon', 'rehab-blocks' ) } initialOpen>
					<TextControl
						label={ __( 'Glyph icon (emoji or character)', 'rehab-blocks' ) }
						value={ icon }
						onChange={ ( v ) => setAttributes( { icon: v } ) }
						help={ __( 'Used if no icon image is set.', 'rehab-blocks' ) }
					/>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( m ) => setAttributes( { iconImage: m.url } ) }
							allowedTypes={ [ 'image' ] }
							render={ ( { open } ) => (
								<Button variant="secondary" onClick={ open } style={ { marginTop: '0.5rem' } }>
									{ iconImage
										? __( 'Replace icon image', 'rehab-blocks' )
										: __( 'Use icon image instead', 'rehab-blocks' ) }
								</Button>
							) }
						/>
					</MediaUploadCheck>
					{ iconImage && (
						<Button
							variant="link"
							isDestructive
							onClick={ () => setAttributes( { iconImage: '' } ) }
							style={ { marginTop: '0.5rem' } }
						>
							{ __( 'Clear icon image', 'rehab-blocks' ) }
						</Button>
					) }
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<div className="rehab-feature__icon">
					{ iconImage ? <img src={ iconImage } alt="" /> : <span>{ icon }</span> }
				</div>
				<RichText
					tagName="h3"
					className="rehab-feature__title"
					value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) }
					placeholder={ __( 'Title', 'rehab-blocks' ) }
					allowedFormats={ [] }
				/>
				<RichText
					tagName="p"
					className="rehab-feature__body"
					value={ body }
					onChange={ ( v ) => setAttributes( { body: v } ) }
					placeholder={ __( 'Body…', 'rehab-blocks' ) }
				/>
			</div>
		</>
	);
}

function save( { attributes } ) {
	const { icon, iconImage, title, body } = attributes;
	const blockProps = useBlockProps.save( { className: 'rehab-feature' } );
	return (
		<div { ...blockProps }>
			<div className="rehab-feature__icon">
				{ iconImage ? <img src={ iconImage } alt="" loading="lazy" /> : <span>{ icon }</span> }
			</div>
			<RichText.Content
				tagName="h3"
				className="rehab-feature__title"
				value={ title }
			/>
			<RichText.Content
				tagName="p"
				className="rehab-feature__body"
				value={ body }
			/>
		</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
