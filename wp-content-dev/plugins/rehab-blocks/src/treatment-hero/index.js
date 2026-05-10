import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, MediaUpload, MediaUploadCheck, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="CTA URLs" initialOpen>
						<TextControl label="Primary URL" value={ a.primaryUrl } onChange={ set( 'primaryUrl' ) } />
						<TextControl label="Secondary URL" value={ a.secondaryUrl } onChange={ set( 'secondaryUrl' ) } />
					</PanelBody>
					<PanelBody title="Image + badge" initialOpen={ false }>
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { imageUrl: m.url, imageAlt: m.alt || '' } ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ a.imageUrl ? 'Replace image' : 'Pick image' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="Image URL" value={ a.imageUrl } onChange={ set( 'imageUrl' ) } />
						<TextControl label="Image alt" value={ a.imageAlt } onChange={ set( 'imageAlt' ) } />
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { badgeImageUrl: m.url } ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open } style={ { marginTop: '0.5rem' } }>{ a.badgeImageUrl ? 'Replace badge' : 'Pick badge image' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="Badge image URL" value={ a.badgeImageUrl } onChange={ set( 'badgeImageUrl' ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className="rehab-treatment-hero">
						<div className="rehab-container">
							<div className="rehab-treatment-hero__grid">
								<div>
									<RichText
										tagName="p"
										className="rehab-treatment-hero__eyebrow"
										value={ a.eyebrow }
										onChange={ set( 'eyebrow' ) }
										placeholder="Eyebrow…"
										allowedFormats={ [] }
									/>
									<RichText
										tagName="h1"
										className="rehab-treatment-hero__h1"
										value={ a.headline }
										onChange={ set( 'headline' ) }
										placeholder="Headline…"
										allowedFormats={ [ 'core/bold', 'core/italic' ] }
									/>
									<RichText
										tagName="p"
										className="rehab-treatment-hero__lede"
										value={ a.lede }
										onChange={ set( 'lede' ) }
										placeholder="Lede paragraph…"
										allowedFormats={ [ 'core/bold', 'core/italic' ] }
									/>
									<div className="rehab-treatment-hero__cta-row">
										<span className="rehab-btn rehab-btn--luxury">
											<RichText tagName="span" value={ a.primaryText } onChange={ set( 'primaryText' ) } placeholder="Primary CTA…" allowedFormats={ [] } />
										</span>
										<span className="rehab-btn rehab-btn--outline">
											<RichText tagName="span" value={ a.secondaryText } onChange={ set( 'secondaryText' ) } placeholder="Secondary CTA…" allowedFormats={ [] } />
										</span>
									</div>
									<RichText
										tagName="p"
										className="rehab-treatment-hero__helper"
										value={ a.helper }
										onChange={ set( 'helper' ) }
										placeholder="Helper text…"
										allowedFormats={ [] }
									/>
									<div className="rehab-treatment-hero__trust">
										{ [ 1, 2, 3 ].map( ( i ) => (
											<div className="rehab-treatment-hero__trust-item" key={ i }>
												<RichText tagName="div" className="num" value={ a[ `stat${ i }Num` ] } onChange={ set( `stat${ i }Num` ) } placeholder="N" allowedFormats={ [] } />
												<RichText tagName="div" className="lbl" value={ a[ `stat${ i }Label` ] } onChange={ set( `stat${ i }Label` ) } placeholder="Stat label…" allowedFormats={ [] } />
											</div>
										) ) }
									</div>
								</div>
								<div className="rehab-treatment-hero__media">
									<div className="rehab-treatment-hero__image-wrap">
										{ a.imageUrl ? <img src={ a.imageUrl } alt={ a.imageAlt } /> : null }
										<div className="rehab-treatment-hero__image-overlay" aria-hidden="true" />
									</div>
									{ ( a.badgeTitle || a.badgeImageUrl ) ? (
										<div className="rehab-treatment-hero__badge">
											{ a.badgeImageUrl ? <img src={ a.badgeImageUrl } alt="" /> : null }
											<div className="rehab-treatment-hero__badge-text">
												<RichText tagName="strong" value={ a.badgeTitle } onChange={ set( 'badgeTitle' ) } placeholder="Badge title" allowedFormats={ [] } />
												<RichText tagName="span" value={ a.badgeText } onChange={ set( 'badgeText' ) } placeholder="Badge text" allowedFormats={ [] } />
											</div>
										</div>
									) : null }
								</div>
							</div>
						</div>
					</section>
				</div>
			</>
		);
	},
	save( { attributes } ) {
		const a = attributes;
		return (
			<section className="rehab-treatment-hero">
				<div className="rehab-container">
					<div className="rehab-treatment-hero__grid">
						<div>
							<RichText.Content tagName="p" className="rehab-treatment-hero__eyebrow" value={ a.eyebrow } />
							<RichText.Content tagName="h1" className="rehab-treatment-hero__h1" value={ a.headline } />
							<RichText.Content tagName="p" className="rehab-treatment-hero__lede" value={ a.lede } />
							<div className="rehab-treatment-hero__cta-row">
								<a href={ a.primaryUrl } className="rehab-btn rehab-btn--luxury"><RichText.Content value={ a.primaryText } /></a>
								<a href={ a.secondaryUrl } className="rehab-btn rehab-btn--outline"><RichText.Content value={ a.secondaryText } /></a>
							</div>
							<RichText.Content tagName="p" className="rehab-treatment-hero__helper" value={ a.helper } />
							<div className="rehab-treatment-hero__trust">
								{ [ 1, 2, 3 ].map( ( i ) => (
									<div className="rehab-treatment-hero__trust-item" key={ i }>
										<RichText.Content tagName="div" className="num" value={ a[ `stat${ i }Num` ] } />
										<RichText.Content tagName="div" className="lbl" value={ a[ `stat${ i }Label` ] } />
									</div>
								) ) }
							</div>
						</div>
						<div className="rehab-treatment-hero__media">
							<div className="rehab-treatment-hero__image-wrap">
								{ a.imageUrl ? <img src={ a.imageUrl } alt={ a.imageAlt } /> : null }
								<div className="rehab-treatment-hero__image-overlay" aria-hidden="true" />
							</div>
							{ ( a.badgeTitle || a.badgeImageUrl ) ? (
								<div className="rehab-treatment-hero__badge">
									{ a.badgeImageUrl ? <img src={ a.badgeImageUrl } alt="" /> : null }
									<div className="rehab-treatment-hero__badge-text">
										<RichText.Content tagName="strong" value={ a.badgeTitle } />
										<RichText.Content tagName="span" value={ a.badgeText } />
									</div>
								</div>
							) : null }
						</div>
					</div>
				</div>
			</section>
		);
	},
} );
