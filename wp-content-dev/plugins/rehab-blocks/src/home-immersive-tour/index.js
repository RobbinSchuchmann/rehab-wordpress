import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const blockProps = useBlockProps( { className: 'drt-tour drt-bg-white' } );

		const rooms = a.rooms || [];
		const setRoom = ( i, key ) => ( v ) => {
			const next = rooms.map( ( r, idx ) => ( idx === i ? { ...r, [ key ]: v } : r ) );
			setAttributes( { rooms: next } );
		};

		const gallery = a.gallery || [];
		const setImg = ( i, key, v ) => {
			const next = gallery.map( ( g, idx ) => ( idx === i ? { ...g, [ key ]: v } : g ) );
			setAttributes( { gallery: next } );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title="360° Tour CTA" initialOpen>
						<TextControl label="Tour URL" value={ a.tourUrl } onChange={ set( 'tourUrl' ) } />
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { heroImage: m.url, heroAlt: m.alt || a.heroAlt } ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ a.heroImage ? 'Replace hero image' : 'Pick hero image' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="Hero image URL" value={ a.heroImage } onChange={ set( 'heroImage' ) } />
						<TextControl label="Hero image alt" value={ a.heroAlt } onChange={ set( 'heroAlt' ) } />
					</PanelBody>
					<PanelBody title="Room shortcuts" initialOpen={ false }>
						{ rooms.map( ( r, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', marginBottom: '0.75rem', paddingBottom: '0.5rem' } }>
								<TextControl label={ `Room ${ i + 1 } label` } value={ r.label } onChange={ setRoom( i, 'label' ) } />
								<TextControl label={ `Room ${ i + 1 } URL` } value={ r.url } onChange={ setRoom( i, 'url' ) } />
								<Button isDestructive variant="link" onClick={ () => setAttributes( { rooms: rooms.filter( ( _, j ) => j !== i ) } ) }>Remove room</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ () => setAttributes( { rooms: [ ...rooms, { label: '', url: '' } ] } ) }>Add room</Button>
					</PanelBody>
					<PanelBody title="Gallery images" initialOpen={ false }>
						{ gallery.map( ( g, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', marginBottom: '0.75rem', paddingBottom: '0.5rem' } }>
								<MediaUploadCheck>
									<MediaUpload onSelect={ ( m ) => setImg( i, 'thumb', m.url ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ g.thumb ? 'Replace thumb' : 'Pick thumb' }</Button> } />
								</MediaUploadCheck>
								<TextControl label={ `Image ${ i + 1 } thumb URL` } value={ g.thumb } onChange={ ( v ) => setImg( i, 'thumb', v ) } />
								<MediaUploadCheck>
									<MediaUpload onSelect={ ( m ) => setImg( i, 'full', m.url ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ g.full ? 'Replace full' : 'Pick full' }</Button> } />
								</MediaUploadCheck>
								<TextControl label={ `Image ${ i + 1 } full URL` } value={ g.full } onChange={ ( v ) => setImg( i, 'full', v ) } />
								<TextControl label={ `Image ${ i + 1 } alt / caption` } value={ g.alt } onChange={ ( v ) => setImg( i, 'alt', v ) } />
								<Button isDestructive variant="link" onClick={ () => setAttributes( { gallery: gallery.filter( ( _, j ) => j !== i ) } ) }>Remove image</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ () => setAttributes( { gallery: [ ...gallery, { thumb: '', full: '', alt: '' } ] } ) }>Add image</Button>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container">
						<RichText tagName="h2" className="drt-heading drt-heading--lg drt-tour__title drt-text-balance" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />

						<div className="drt-tour__hero">
							<div className="drt-tour__hero-wrap">
								<span className="drt-tour__hero-link">
									{ a.heroImage ? <img src={ a.heroImage } alt={ a.heroAlt } className="drt-tour__hero-image" /> : <span className="drt-tour__hero-image">Pick a hero image…</span> }
									<span className="drt-tour__hero-overlay" aria-hidden="true"></span>
								</span>
								<div className="drt-tour__hero-center">
									<span className="drt-btn drt-btn--luxury drt-tour__hero-btn">
										<RichText tagName="span" value={ a.ctaText } onChange={ set( 'ctaText' ) } placeholder="START 360° TOUR" allowedFormats={ [] } />
									</span>
									<div className="drt-tour__shortcuts drt-tour__shortcuts--desktop">
										{ rooms.map( ( r, i ) => <span key={ i } className="drt-btn drt-btn--ghost">{ r.label }</span> ) }
									</div>
								</div>
							</div>
							<div className="drt-tour__shortcuts drt-tour__shortcuts--mobile">
								{ rooms.map( ( r, i ) => <span key={ i } className="drt-btn drt-btn--outline">{ r.label }</span> ) }
							</div>
						</div>

						<div className="drt-tour__gallery">
							<div className="swiper drt-tour__swiper">
								<div className="swiper-wrapper">
									{ gallery.map( ( g, i ) => (
										<div className="swiper-slide" key={ i }>
											<span className="drt-tour__slide">
												{ g.thumb ? <img src={ g.thumb } alt={ g.alt } /> : <span>{ g.alt || `Image ${ i + 1 }` }</span> }
											</span>
										</div>
									) ) }
								</div>
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
