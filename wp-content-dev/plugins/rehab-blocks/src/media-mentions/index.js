import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
} from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, Placeholder } from '@wordpress/components';
import { gallery as galleryIcon } from '@wordpress/icons';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

function Edit( { attributes, setAttributes } ) {
	const { background, heading, logos } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-media-mentions rehab-bg-${ background }`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Media Mentions', 'rehab-blocks' ) } initialOpen>
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
				<div className="rehab-container">
					<RichText
						tagName="p"
						className="rehab-media-mentions__heading"
						value={ heading }
						onChange={ ( v ) => setAttributes( { heading: v } ) }
						placeholder={ __( 'Heading…', 'rehab-blocks' ) }
						allowedFormats={ [] }
					/>
					{ logos.length === 0 ? (
						<MediaUploadCheck>
							<MediaUpload
								multiple
								gallery
								onSelect={ ( media ) =>
									setAttributes( {
										logos: media.map( ( m ) => ( { id: m.id, url: m.url, alt: m.alt || '' } ) ),
									} )
								}
								allowedTypes={ [ 'image' ] }
								value={ logos.map( ( l ) => l.id ) }
								render={ ( { open } ) => (
									<Placeholder icon={ galleryIcon } label={ __( 'Logos', 'rehab-blocks' ) }>
										<Button variant="primary" onClick={ open }>
											{ __( 'Pick logo images', 'rehab-blocks' ) }
										</Button>
									</Placeholder>
								) }
							/>
						</MediaUploadCheck>
					) : (
						<>
							<div className="rehab-media-mentions__grid">
								{ logos.map( ( logo, idx ) => (
									<img
										key={ idx }
										src={ logo.url }
										alt={ logo.alt || '' }
										className="rehab-media-mentions__logo"
									/>
								) ) }
							</div>
							<MediaUploadCheck>
								<MediaUpload
									multiple
									gallery
									onSelect={ ( media ) =>
										setAttributes( {
											logos: media.map( ( m ) => ( { id: m.id, url: m.url, alt: m.alt || '' } ) ),
										} )
									}
									allowedTypes={ [ 'image' ] }
									value={ logos.map( ( l ) => l.id ) }
									render={ ( { open } ) => (
										<Button variant="secondary" onClick={ open } style={ { marginTop: '1rem' } }>
											{ __( 'Edit logos', 'rehab-blocks' ) }
										</Button>
									) }
								/>
							</MediaUploadCheck>
						</>
					) }
				</div>
			</section>
		</>
	);
}

function save( { attributes } ) {
	const { background, heading, logos } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-media-mentions rehab-bg-${ background }`,
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container">
				{ heading && (
					<RichText.Content
						tagName="p"
						className="rehab-media-mentions__heading"
						value={ heading }
					/>
				) }
				<div className="rehab-media-mentions__grid">
					{ logos.map( ( logo, idx ) => (
						<img
							key={ idx }
							src={ logo.url }
							alt={ logo.alt || '' }
							className="rehab-media-mentions__logo"
							loading="lazy"
						/>
					) ) }
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
