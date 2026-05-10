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
	const { background, imagePosition, imageUrl, imageId, imageAlt, eyebrow, heading, body, features } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-accommodation rehab-bg-${ background } rehab-accommodation--image-${ imagePosition }`,
	} );

	const updateFeature = ( idx, value ) =>
		setAttributes( { features: features.map( ( f, i ) => ( i === idx ? value : f ) ) } );

	const addFeature = () => setAttributes( { features: [ ...features, 'New feature' ] } );

	const removeFeature = ( idx ) =>
		setAttributes( { features: features.filter( ( _, i ) => i !== idx ) } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Accommodation', 'rehab-blocks' ) } initialOpen>
					<SelectControl
						label={ __( 'Image position', 'rehab-blocks' ) }
						value={ imagePosition }
						options={ [
							{ label: 'Left', value: 'left' },
							{ label: 'Right', value: 'right' },
						] }
						onChange={ ( v ) => setAttributes( { imagePosition: v } ) }
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
					<TextControl
						label={ __( 'Image alt', 'rehab-blocks' ) }
						value={ imageAlt }
						onChange={ ( v ) => setAttributes( { imageAlt: v } ) }
					/>
					<Button variant="secondary" onClick={ addFeature } style={ { marginTop: '1rem' } }>
						{ __( '+ Add feature', 'rehab-blocks' ) }
					</Button>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="rehab-container">
					<div className="rehab-accommodation__grid">
						<div className="rehab-accommodation__media">
							{ imageUrl ? (
								<img src={ imageUrl } alt={ imageAlt || heading } />
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
											<Placeholder icon={ imageIcon } label={ __( 'Image', 'rehab-blocks' ) }>
												<Button variant="primary" onClick={ open }>
													{ __( 'Choose image', 'rehab-blocks' ) }
												</Button>
											</Placeholder>
										) }
									/>
								</MediaUploadCheck>
							) }
						</div>
						<div className="rehab-accommodation__content">
							<RichText
								tagName="span"
								className="rehab-eyebrow"
								value={ eyebrow }
								onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
								placeholder={ __( 'Eyebrow', 'rehab-blocks' ) }
								allowedFormats={ [] }
							/>
							<RichText
								tagName="h2"
								className="rehab-heading rehab-heading--lg"
								value={ heading }
								onChange={ ( v ) => setAttributes( { heading: v } ) }
								placeholder={ __( 'Heading', 'rehab-blocks' ) }
								allowedFormats={ [ 'core/bold', 'core/italic' ] }
							/>
							<RichText
								tagName="p"
								className="rehab-accommodation__body"
								value={ body }
								onChange={ ( v ) => setAttributes( { body: v } ) }
								placeholder={ __( 'Body…', 'rehab-blocks' ) }
							/>
							<ul className="rehab-accommodation__features">
								{ features.map( ( f, idx ) => (
									<li key={ idx }>
										<span className="rehab-accommodation__diamond" aria-hidden="true">◆</span>
										<RichText
											tagName="span"
											value={ f }
											onChange={ ( v ) => updateFeature( idx, v ) }
											placeholder={ __( 'Feature', 'rehab-blocks' ) }
											allowedFormats={ [] }
										/>
										<button
											type="button"
											className="rehab-accommodation__remove"
											onClick={ () => removeFeature( idx ) }
											aria-label={ __( 'Remove', 'rehab-blocks' ) }
										>
											×
										</button>
									</li>
								) ) }
							</ul>
						</div>
					</div>
				</div>
			</section>
		</>
	);
}

function save( { attributes } ) {
	const { background, imagePosition, imageUrl, imageAlt, eyebrow, heading, body, features } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-accommodation rehab-bg-${ background } rehab-accommodation--image-${ imagePosition }`,
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container">
				<div className="rehab-accommodation__grid">
					{ imageUrl && (
						<div className="rehab-accommodation__media">
							<img src={ imageUrl } alt={ imageAlt || '' } loading="lazy" />
						</div>
					) }
					<div className="rehab-accommodation__content">
						{ eyebrow && (
							<RichText.Content
								tagName="span"
								className="rehab-eyebrow"
								value={ eyebrow }
							/>
						) }
						<RichText.Content
							tagName="h2"
							className="rehab-heading rehab-heading--lg"
							value={ heading }
						/>
						{ body && (
							<RichText.Content
								tagName="p"
								className="rehab-accommodation__body"
								value={ body }
							/>
						) }
						{ features.length > 0 && (
							<ul className="rehab-accommodation__features">
								{ features.map( ( f, idx ) => (
									<li key={ idx }>
										<span className="rehab-accommodation__diamond" aria-hidden="true">◆</span>
										<RichText.Content tagName="span" value={ f } />
									</li>
								) ) }
							</ul>
						) }
					</div>
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
