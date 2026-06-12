import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

const EMPTY_ITEM = { name: '', duration: '', tone: '1', quote: '', who: '', videoUrl: '', posterUrl: '' };

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const items = a.items || [];
		const setItem = ( i, key ) => ( v ) => {
			const next = items.map( ( it, idx ) => ( idx === i ? { ...it, [ key ]: v } : it ) );
			setAttributes( { items: next } );
		};
		return (
			<>
				<InspectorControls>
					<PanelBody title="Rating chip" initialOpen={ false }>
						<ToggleControl label="Show rating" checked={ a.showRating } onChange={ set( 'showRating' ) } />
						<TextControl label="Score" value={ a.ratingScore } onChange={ set( 'ratingScore' ) } />
						<TextControl label="Rating text" value={ a.ratingText } onChange={ set( 'ratingText' ) } />
					</PanelBody>
					{ items.map( ( item, i ) => (
						<PanelBody key={ i } title={ `Video ${ i + 1 } — ${ item.name || 'untitled' }` } initialOpen={ false }>
							<TextControl label="Video URL (mp4 / YouTube / Vimeo)" value={ item.videoUrl } onChange={ setItem( i, 'videoUrl' ) } />
							<TextControl label="Poster image URL" value={ item.posterUrl } onChange={ setItem( i, 'posterUrl' ) } />
							<TextControl label="Duration" value={ item.duration } onChange={ setItem( i, 'duration' ) } />
							<SelectControl
								label="Placeholder tone"
								value={ item.tone }
								options={ [
									{ label: 'Sage light', value: '1' },
									{ label: 'Sage deep', value: '2' },
									{ label: 'Warm sand', value: '3' },
									{ label: 'Dark silhouette (anonymous)', value: '4' },
								] }
								onChange={ setItem( i, 'tone' ) }
							/>
							<Button
								isDestructive
								variant="secondary"
								onClick={ () => setAttributes( { items: items.filter( ( _, idx ) => idx !== i ) } ) }
							>
								Remove this video
							</Button>
						</PanelBody>
					) ) }
					<PanelBody title="Add" initialOpen={ false }>
						<Button variant="primary" onClick={ () => setAttributes( { items: [ ...items, { ...EMPTY_ITEM } ] } ) }>
							Add video card
						</Button>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className="rehab-video-reel rehab-bg-cream">
						<div className="rehab-container">
							<div className="rehab-video-reel__head">
								<div>
									<RichText tagName="span" className="rehab-video-reel__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
									<RichText tagName="h2" className="rehab-video-reel__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
								</div>
								{ a.showRating ? (
									<div className="rehab-video-reel__rating"><span>★★★★★ <strong>{ a.ratingScore }</strong> { a.ratingText }</span></div>
								) : null }
							</div>
							<div className="rehab-video-reel__grid">
								{ items.map( ( item, i ) => (
									<div className="rehab-video-card" key={ i }>
										<div className={ `rehab-video-card__thumb rehab-video-card__thumb--tone-${ item.tone || '1' }` }>
											{ item.posterUrl ? <img className="rehab-video-card__poster" src={ item.posterUrl } alt="" /> : null }
											{ item.duration ? <span className="rehab-video-card__duration">▶ { item.duration }</span> : null }
											<span className="rehab-video-card__play">▶</span>
											<RichText tagName="span" className="rehab-video-card__name" value={ item.name } onChange={ setItem( i, 'name' ) } placeholder="Name · format" allowedFormats={ [] } />
										</div>
										<div className="rehab-video-card__caption">
											<RichText tagName="div" className="rehab-video-card__quote" value={ item.quote } onChange={ setItem( i, 'quote' ) } placeholder="“Quote…”" allowedFormats={ [] } />
											<RichText tagName="div" className="rehab-video-card__who" value={ item.who } onChange={ setItem( i, 'who' ) } placeholder="Consent / format note" allowedFormats={ [] } />
										</div>
									</div>
								) ) }
							</div>
						</div>
					</section>
				</div>
			</>
		);
	},
	save: () => null,
} );
