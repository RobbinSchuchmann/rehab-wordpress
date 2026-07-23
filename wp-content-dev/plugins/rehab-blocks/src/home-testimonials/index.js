import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const blockProps = useBlockProps( { className: 'drt-testimonials drt-bg-white drt-section' } );

		const videos = a.videos || [];
		const reviews = a.reviews || [];

		const setVideo = ( i, key ) => ( v ) => {
			const next = videos.map( ( s, idx ) => ( idx === i ? { ...s, [ key ]: v } : s ) );
			setAttributes( { videos: next } );
		};
		const setReview = ( i, key ) => ( v ) => {
			const next = reviews.map( ( s, idx ) => ( idx === i ? { ...s, [ key ]: v } : s ) );
			setAttributes( { reviews: next } );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title="Video testimonials" initialOpen>
						{ videos.map( ( v, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', marginBottom: '0.75rem', paddingBottom: '0.5rem' } }>
								<TextControl label={ `Video ${ i + 1 } — YouTube ID` } value={ v.id || '' } onChange={ setVideo( i, 'id' ) } />
								<TextControl label="Caption" value={ v.caption || '' } onChange={ setVideo( i, 'caption' ) } />
								<Button isDestructive variant="link" onClick={ () => setAttributes( { videos: videos.filter( ( _, j ) => j !== i ) } ) }>Remove video</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ () => setAttributes( { videos: [ ...videos, { id: '', caption: '' } ] } ) }>Add video</Button>
					</PanelBody>
					<PanelBody title="Reviews" initialOpen={ false }>
						{ reviews.map( ( r, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', marginBottom: '0.75rem', paddingBottom: '0.5rem' } }>
								<TextControl label={ `Review ${ i + 1 } — Name` } value={ r.name || '' } onChange={ setReview( i, 'name' ) } />
								<TextControl label="Time" value={ r.time || '' } onChange={ setReview( i, 'time' ) } />
								<TextControl label="Initial" value={ r.initial || '' } onChange={ setReview( i, 'initial' ) } />
								<TextControl label="Avatar color (hex)" value={ r.color || '' } onChange={ setReview( i, 'color' ) } />
								<TextControl label="Source (Google or Recovery.com)" value={ r.source || '' } onChange={ setReview( i, 'source' ) } />
								<TextareaControl label="Review text" value={ r.content || '' } onChange={ setReview( i, 'content' ) } rows={ 5 } />
								<Button isDestructive variant="link" onClick={ () => setAttributes( { reviews: reviews.filter( ( _, j ) => j !== i ) } ) }>Remove review</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ () => setAttributes( { reviews: [ ...reviews, { name: '', time: '', initial: '', color: '#5B7FD3', source: 'Google', content: '' } ] } ) }>Add review</Button>
					</PanelBody>
				</InspectorControls>

				<section { ...blockProps }>
					<div className="drt-container">
						<div className="drt-section-header">
							<RichText tagName="h2" className="drt-heading drt-heading--lg" value={ a.videoHeading } onChange={ set( 'videoHeading' ) } placeholder="Video section heading…" allowedFormats={ [] } />
							<RichText tagName="p" className="drt-body" value={ a.videoIntro } onChange={ set( 'videoIntro' ) } placeholder="Video section intro…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
						</div>

						{ /* Editor preview of the video-reel cards (REH-180); the
						     front-end reuses rehab/video-reel's built CSS + view.js. */ }
						<div className="rehab-video-reel rehab-video-reel--home">
							<div className="rehab-video-reel__grid">
								{ videos.map( ( v, i ) => (
									<div className="rehab-video-card" key={ i }>
										<div className="rehab-video-card__thumb rehab-video-card__thumb--tone-1">
											{ v.id ? <img className="rehab-video-card__poster" src={ `https://i.ytimg.com/vi/${ v.id }/maxresdefault.jpg` } alt={ v.caption || '' } /> : null }
											<span className="rehab-video-card__play" aria-hidden="true">
												<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z" /></svg>
											</span>
										</div>
										{ v.caption ? (
											<div className="rehab-video-card__caption">
												<div className="rehab-video-card__quote">{ v.caption }</div>
											</div>
										) : null }
									</div>
								) ) }
							</div>
						</div>

						<div className="drt-testimonials__reviews">
							<div className="drt-section-header">
								<RichText tagName="h2" className="drt-heading drt-heading--lg" value={ a.reviewsHeading } onChange={ set( 'reviewsHeading' ) } placeholder="Reviews section heading…" allowedFormats={ [] } />
								<RichText tagName="p" className="drt-body" value={ a.reviewsIntro } onChange={ set( 'reviewsIntro' ) } placeholder="Reviews section intro…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
							</div>
							<div className="drt-testimonials__reviews-carousel">
								{ reviews.map( ( r, i ) => (
									<div className="drt-card--review" key={ i }>
										<div className="drt-card--review__header">
											<div className="drt-card--review__author">
												<div className="drt-card--review__avatar" style={ { backgroundColor: r.color } }>{ r.initial }</div>
												<div>
													<p className="drt-card--review__name">{ r.name }</p>
													<p className="drt-card--review__time">{ r.time }</p>
												</div>
											</div>
											<span className="drt-card--review__source">{ r.source }</span>
										</div>
										<div className="drt-card--review__content">
											<p className="drt-card--review__text">{ r.content }</p>
										</div>
									</div>
								) ) }
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
