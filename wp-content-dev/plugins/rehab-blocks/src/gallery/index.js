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

function Edit( { attributes, setAttributes } ) {
	const { background, variant, columns, heading, images } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-gallery rehab-bg-${ background } rehab-gallery--${ variant } rehab-gallery--cols-${ columns }`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Gallery settings', 'rehab-blocks' ) } initialOpen>
					<SelectControl
						label={ __( 'Variant', 'rehab-blocks' ) }
						value={ variant }
						options={ [
							{ label: 'Grid (uniform tiles)', value: 'grid' },
							{ label: 'Masonry (varying heights)', value: 'masonry' },
						] }
						onChange={ ( v ) => setAttributes( { variant: v } ) }
					/>
					<SelectControl
						label={ __( 'Columns', 'rehab-blocks' ) }
						value={ String( columns ) }
						options={ [
							{ label: '2', value: '2' },
							{ label: '3', value: '3' },
							{ label: '4', value: '4' },
						] }
						onChange={ ( v ) => setAttributes( { columns: parseInt( v, 10 ) } ) }
					/>
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
					{ ( heading || images.length === 0 ) && (
						<header className="rehab-gallery__header">
							<RichText
								tagName="h2"
								className="rehab-heading rehab-heading--lg"
								value={ heading }
								onChange={ ( v ) => setAttributes( { heading: v } ) }
								placeholder={ __( 'Optional gallery heading…', 'rehab-blocks' ) }
								allowedFormats={ [ 'core/bold', 'core/italic' ] }
							/>
						</header>
					) }
					{ images.length === 0 ? (
						<MediaUploadCheck>
							<MediaUpload
								multiple
								gallery
								onSelect={ ( media ) =>
									setAttributes( {
										images: media.map( ( m ) => ( {
											id: m.id,
											url: m.url,
											alt: m.alt || '',
										} ) ),
									} )
								}
								allowedTypes={ [ 'image' ] }
								value={ images.map( ( i ) => i.id ) }
								render={ ( { open } ) => (
									<Placeholder icon={ galleryIcon } label={ __( 'Gallery', 'rehab-blocks' ) }>
										<Button variant="primary" onClick={ open }>
											{ __( 'Pick images', 'rehab-blocks' ) }
										</Button>
									</Placeholder>
								) }
							/>
						</MediaUploadCheck>
					) : (
						<>
							<div className="rehab-gallery__grid">
								{ images.map( ( img, idx ) => (
									<figure className="rehab-gallery__item" key={ idx }>
										<img src={ img.url } alt={ img.alt || '' } loading="lazy" />
									</figure>
								) ) }
							</div>
							<MediaUploadCheck>
								<MediaUpload
									multiple
									gallery
									onSelect={ ( media ) =>
										setAttributes( {
											images: media.map( ( m ) => ( {
												id: m.id,
												url: m.url,
												alt: m.alt || '',
											} ) ),
										} )
									}
									allowedTypes={ [ 'image' ] }
									value={ images.map( ( i ) => i.id ) }
									render={ ( { open } ) => (
										<Button
											variant="secondary"
											onClick={ open }
											style={ { marginTop: '1rem' } }
										>
											{ __( 'Edit images', 'rehab-blocks' ) }
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
	const { background, variant, columns, heading, images } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-gallery rehab-bg-${ background } rehab-gallery--${ variant } rehab-gallery--cols-${ columns }`,
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container">
				{ heading && (
					<header className="rehab-gallery__header">
						<RichText.Content
							tagName="h2"
							className="rehab-heading rehab-heading--lg"
							value={ heading }
						/>
					</header>
				) }
				<div className="rehab-gallery__grid">
					{ images.map( ( img, idx ) => (
						<figure className="rehab-gallery__item" key={ idx }>
							<img src={ img.url } alt={ img.alt || '' } loading="lazy" />
						</figure>
					) ) }
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
