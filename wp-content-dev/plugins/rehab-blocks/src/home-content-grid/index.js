import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const articles = a.articles || [];
		const setArticle = ( i, key ) => ( v ) => {
			const next = articles.map( ( art, idx ) => ( idx === i ? { ...art, [ key ]: v } : art ) );
			setAttributes( { articles: next } );
		};
		const blockProps = useBlockProps( { className: 'drt-content-grid drt-bg-white drt-section--lg' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Articles" initialOpen>
						{ articles.map( ( art, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', marginBottom: '0.75rem', paddingBottom: '0.5rem' } }>
								<TextControl label={ `Article ${ i + 1 } title` } value={ art.title || '' } onChange={ setArticle( i, 'title' ) } />
								<TextControl label="Link URL" value={ art.link || '' } onChange={ setArticle( i, 'link' ) } />
								<MediaUploadCheck>
									<MediaUpload
										onSelect={ ( m ) => setArticle( i, 'imageUrl' )( m.url ) }
										allowedTypes={ [ 'image' ] }
										render={ ( { open } ) => (
											<Button variant="secondary" onClick={ open }>{ art.imageUrl ? 'Replace image' : 'Pick image' }</Button>
										) }
									/>
								</MediaUploadCheck>
								<TextControl label="Image URL" value={ art.imageUrl || '' } onChange={ setArticle( i, 'imageUrl' ) } />
								<Button isDestructive variant="link" onClick={ () => setAttributes( { articles: articles.filter( ( _, j ) => j !== i ) } ) }>Remove article</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ () => setAttributes( { articles: [ ...articles, { title: '', imageUrl: '', link: '' } ] } ) }>Add article</Button>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container drt-container--narrow">
						<div className="drt-section-header">
							<RichText
								tagName="h2"
								className="drt-heading drt-heading--md"
								value={ a.heading }
								onChange={ set( 'heading' ) }
								placeholder="Heading…"
								allowedFormats={ [] }
							/>
						</div>
						<div className="drt-content-grid__grid">
							{ articles.map( ( art, i ) => (
								<a href={ art.link || '#' } className="drt-card--article" key={ i } onClick={ ( e ) => e.preventDefault() }>
									<div className="drt-card--article__image">
										{ art.imageUrl ? (
											<img src={ art.imageUrl } alt={ art.title || '' } loading="lazy" />
										) : (
											<span>Image</span>
										) }
									</div>
									<h3 className="drt-card--article__title">{ art.title || '' }</h3>
								</a>
							) ) }
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
