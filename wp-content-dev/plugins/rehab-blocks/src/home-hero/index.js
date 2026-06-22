import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const blockProps = useBlockProps( { className: 'drt-hero' } );
		const trust = a.trustItems || [];
		const setTrust = ( i ) => ( v ) => {
			setAttributes( { trustItems: trust.map( ( t, idx ) => ( idx === i ? v : t ) ) } );
		};
		const heroImg = a.imageUrl || ( window.rehabHomeHero && window.rehabHomeHero.heroImg ) || '';
		return (
			<>
				<InspectorControls>
					<PanelBody title="Hero image" initialOpen>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( m ) => setAttributes( { imageUrl: m.url, imageAlt: m.alt || a.imageAlt } ) }
								allowedTypes={ [ 'image' ] }
								render={ ( { open } ) => (
									<Button variant="secondary" onClick={ open }>{ a.imageUrl ? 'Replace image' : 'Pick image' }</Button>
								) }
							/>
						</MediaUploadCheck>
						<TextControl label="Image URL" value={ a.imageUrl } onChange={ set( 'imageUrl' ) } help="Leave blank to use the default bundled hero image." />
						<TextControl label="Image alt" value={ a.imageAlt } onChange={ set( 'imageAlt' ) } />
					</PanelBody>
					<PanelBody title="CTA" initialOpen={ false }>
						<TextControl label="Button text" value={ a.ctaText } onChange={ set( 'ctaText' ) } />
						<TextControl label="Button URL" value={ a.ctaUrl } onChange={ set( 'ctaUrl' ) } />
					</PanelBody>
					<PanelBody title="Video" initialOpen={ false }>
						<TextControl label="YouTube video ID" value={ a.videoId } onChange={ set( 'videoId' ) } help="The id only, e.g. rlEKwU70eGY." />
					</PanelBody>
					<PanelBody title="Trust signals" initialOpen={ false }>
						{ trust.map( ( t, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', marginBottom: '0.75rem', paddingBottom: '0.5rem' } }>
								<TextControl label={ `Item ${ i + 1 }` } value={ t } onChange={ setTrust( i ) } />
								<Button isDestructive variant="link" onClick={ () => setAttributes( { trustItems: trust.filter( ( _, j ) => j !== i ) } ) }>Remove item</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ () => setAttributes( { trustItems: [ ...trust, '' ] } ) }>Add trust signal</Button>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container">
						<div className="drt-hero__grid">
							<div className="drt-hero__content drt-animate-fade-in-left">
								<h1 className="drt-hero__h1">
									<RichText tagName="span" className="drt-hero__voted" value={ a.voted } onChange={ set( 'voted' ) } placeholder="Voted…" allowedFormats={ [] } />
									<RichText tagName="span" className="drt-hero__headline" value={ a.headline } onChange={ set( 'headline' ) } placeholder="Headline…" allowedFormats={ [] } />
								</h1>
								<RichText tagName="p" className="drt-hero__body" value={ a.body } onChange={ set( 'body' ) } placeholder="Body…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
								<div className="drt-hero__cta">
									<a href={ a.ctaUrl || '#' } className="drt-btn drt-btn--luxury" onClick={ ( e ) => e.preventDefault() }>
										<RichText tagName="span" value={ a.ctaText } onChange={ set( 'ctaText' ) } placeholder="Button…" allowedFormats={ [] } />
									</a>
									<RichText tagName="p" className="drt-hero__cta-helper" value={ a.ctaHelper } onChange={ set( 'ctaHelper' ) } placeholder="Helper text…" allowedFormats={ [] } />
								</div>
								<div className="drt-hero__trust">
									{ ( trust.length ? trust : [ 'Exclusive 12-client intake', 'Intl. multi-disciplinary team', 'Relapse prevention guarantee' ] ).map( ( signal, i ) => (
										<div className="drt-hero__trust-item" key={ i }>
											<span className="drt-hero__diamond" aria-hidden="true">&#9670;</span>
											<span>{ signal }</span>
										</div>
									) ) }
								</div>
							</div>
							<div className="drt-hero__media drt-animate-fade-in-right">
								<div className="drt-hero__image-wrap">
									{ heroImg
										? <img src={ heroImg } alt={ a.imageAlt } className="drt-hero__image" />
										: <div className="drt-hero__image" style={ { background: '#d8d2c8', minHeight: '320px' } } aria-hidden="true" /> }
									<div className="drt-hero__overlay" aria-hidden="true"></div>
									<div className="drt-hero__play" aria-label="Play video tour">
										<svg width="28" height="28" viewBox="0 0 24 24" fill="white" aria-hidden="true"><polygon points="5,3 19,12 5,21"/></svg>
									</div>
								</div>
								<div className="drt-hero__deco" aria-hidden="true"></div>
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
