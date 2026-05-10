import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
	URLInput,
	BlockControls,
} from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	TextControl,
	ToggleControl,
	ToolbarGroup,
	ToolbarButton,
	Placeholder,
} from '@wordpress/components';
import { image as imageIcon, replace as replaceIcon } from '@wordpress/icons';

export default function Edit( { attributes, setAttributes } ) {
	const {
		eyebrow,
		headline,
		body,
		buttonText,
		buttonUrl,
		buttonHelper,
		trustItem1,
		trustItem2,
		trustItem3,
		imageUrl,
		imageId,
		imageAlt,
		videoId,
		showDeco,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'rehab-hero',
		'aria-label': 'Hero',
	} );

	const onSelectImage = ( media ) => {
		setAttributes( {
			imageUrl: media.url,
			imageId: media.id,
			imageAlt: media.alt || '',
		} );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Button', 'rehab-blocks' ) } initialOpen>
					<p style={ { marginTop: 0, fontSize: '12px', opacity: 0.7 } }>
						{ __( 'Button URL', 'rehab-blocks' ) }
					</p>
					<URLInput
						value={ buttonUrl }
						onChange={ ( v ) => setAttributes( { buttonUrl: v } ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Image / Video', 'rehab-blocks' ) }>
					<TextControl
						label={ __( 'Image alt text', 'rehab-blocks' ) }
						value={ imageAlt }
						onChange={ ( v ) => setAttributes( { imageAlt: v } ) }
						help={ __( 'Describe the image for accessibility.', 'rehab-blocks' ) }
					/>
					<TextControl
						label={ __( 'YouTube video ID (optional)', 'rehab-blocks' ) }
						value={ videoId }
						onChange={ ( v ) => setAttributes( { videoId: v } ) }
						help={ __(
							'Paste only the video ID, e.g. rlEKwU70eGY. When set, a play button overlays the image.',
							'rehab-blocks'
						) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Decoration', 'rehab-blocks' ) }>
					<ToggleControl
						label={ __( 'Show decorative frame', 'rehab-blocks' ) }
						checked={ showDeco }
						onChange={ ( v ) => setAttributes( { showDeco: v } ) }
					/>
				</PanelBody>
			</InspectorControls>

			{ imageUrl && (
				<BlockControls>
					<ToolbarGroup>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ onSelectImage }
								allowedTypes={ [ 'image' ] }
								value={ imageId }
								render={ ( { open } ) => (
									<ToolbarButton
										icon={ replaceIcon }
										label={ __( 'Replace image', 'rehab-blocks' ) }
										onClick={ open }
									/>
								) }
							/>
						</MediaUploadCheck>
					</ToolbarGroup>
				</BlockControls>
			) }

			<section { ...blockProps }>
				<div className="rehab-hero__container">
					<div className="rehab-hero__grid">
						<div className="rehab-hero__content">
							<RichText
								tagName="p"
								className="rehab-hero__eyebrow"
								value={ eyebrow }
								onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
								placeholder={ __( 'Eyebrow text…', 'rehab-blocks' ) }
								allowedFormats={ [] }
							/>
							<RichText
								tagName="h1"
								className="rehab-hero__h1"
								value={ headline }
								onChange={ ( v ) => setAttributes( { headline: v } ) }
								placeholder={ __( 'Headline (use Shift+Enter for line breaks)…', 'rehab-blocks' ) }
								allowedFormats={ [ 'core/bold', 'core/italic' ] }
							/>
							<RichText
								tagName="p"
								className="rehab-hero__body"
								value={ body }
								onChange={ ( v ) => setAttributes( { body: v } ) }
								placeholder={ __( 'Body paragraph…', 'rehab-blocks' ) }
							/>
							<div className="rehab-hero__cta">
								<RichText
									tagName="span"
									className="rehab-btn rehab-btn--luxury"
									value={ buttonText }
									onChange={ ( v ) => setAttributes( { buttonText: v } ) }
									placeholder={ __( 'Button text', 'rehab-blocks' ) }
									allowedFormats={ [] }
								/>
								<RichText
									tagName="p"
									className="rehab-hero__cta-helper"
									value={ buttonHelper }
									onChange={ ( v ) => setAttributes( { buttonHelper: v } ) }
									placeholder={ __( 'Helper text under button…', 'rehab-blocks' ) }
								/>
							</div>
							<div className="rehab-hero__trust">
								{ [
									{ key: 'trustItem1', value: trustItem1 },
									{ key: 'trustItem2', value: trustItem2 },
									{ key: 'trustItem3', value: trustItem3 },
								].map( ( item ) => (
									<div className="rehab-hero__trust-item" key={ item.key }>
										<span className="rehab-hero__diamond" aria-hidden="true">◆</span>
										<RichText
											tagName="span"
											value={ item.value }
											onChange={ ( v ) =>
												setAttributes( { [ item.key ]: v } )
											}
											placeholder={ __( 'Trust item…', 'rehab-blocks' ) }
											allowedFormats={ [] }
										/>
									</div>
								) ) }
							</div>
						</div>

						<div className="rehab-hero__media">
							{ imageUrl ? (
								<div className="rehab-hero__image-wrap">
									<img
										src={ imageUrl }
										alt={ imageAlt || '' }
										className="rehab-hero__image"
									/>
									<div className="rehab-hero__overlay" aria-hidden="true" />
									{ videoId && (
										<div
											className="rehab-hero__play"
											aria-label={ __( 'Play video tour', 'rehab-blocks' ) }
										>
											<svg
												width="28"
												height="28"
												viewBox="0 0 24 24"
												fill="white"
												aria-hidden="true"
											>
												<polygon points="5,3 19,12 5,21" />
											</svg>
										</div>
									) }
								</div>
							) : (
								<MediaUploadCheck>
									<MediaUpload
										onSelect={ onSelectImage }
										allowedTypes={ [ 'image' ] }
										value={ imageId }
										render={ ( { open } ) => (
											<Placeholder
												icon={ imageIcon }
												label={ __( 'Hero image', 'rehab-blocks' ) }
												instructions={ __(
													'Pick the right-side hero image.',
													'rehab-blocks'
												) }
											>
												<Button variant="primary" onClick={ open }>
													{ __( 'Choose image', 'rehab-blocks' ) }
												</Button>
											</Placeholder>
										) }
									/>
								</MediaUploadCheck>
							) }
							{ showDeco && (
								<div className="rehab-hero__deco" aria-hidden="true" />
							) }
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
