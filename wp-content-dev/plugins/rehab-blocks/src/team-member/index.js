import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
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
	const { imageUrl, imageId, imageAlt, name, role } = attributes;
	const blockProps = useBlockProps( { className: 'rehab-team-member is-editor' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Team member', 'rehab-blocks' ) } initialOpen>
					<TextControl
						label={ __( 'Image alt text', 'rehab-blocks' ) }
						value={ imageAlt }
						onChange={ ( v ) => setAttributes( { imageAlt: v } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ imageUrl ? (
					<img className="rehab-team-member__photo" src={ imageUrl } alt={ imageAlt || '' } />
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
								<Placeholder icon={ imageIcon } label={ __( 'Photo', 'rehab-blocks' ) }>
									<Button variant="primary" onClick={ open }>
										{ __( 'Choose', 'rehab-blocks' ) }
									</Button>
								</Placeholder>
							) }
						/>
					</MediaUploadCheck>
				) }
				<div className="rehab-team-member__overlay">
					<RichText
						tagName="h3"
						className="rehab-team-member__name"
						value={ name }
						onChange={ ( v ) => setAttributes( { name: v } ) }
						placeholder={ __( 'Name', 'rehab-blocks' ) }
						allowedFormats={ [] }
					/>
					<RichText
						tagName="p"
						className="rehab-team-member__title"
						value={ role }
						onChange={ ( v ) => setAttributes( { role: v } ) }
						placeholder={ __( 'Role', 'rehab-blocks' ) }
						allowedFormats={ [] }
					/>
				</div>
			</div>
		</>
	);
}

function save( { attributes } ) {
	const { imageUrl, imageAlt, name, role } = attributes;
	const blockProps = useBlockProps.save( { className: 'rehab-team-member' } );
	return (
		<div { ...blockProps }>
			{ imageUrl && (
				<img
					className="rehab-team-member__photo"
					src={ imageUrl }
					alt={ imageAlt || name || '' }
					loading="lazy"
				/>
			) }
			<div className="rehab-team-member__overlay">
				<RichText.Content
					tagName="h3"
					className="rehab-team-member__name"
					value={ name }
				/>
				<RichText.Content
					tagName="p"
					className="rehab-team-member__title"
					value={ role }
				/>
			</div>
		</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
