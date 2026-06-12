import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const stats = a.stats || [];
		const setStat = ( i, key ) => ( v ) => {
			const next = stats.map( ( s, idx ) => ( idx === i ? { ...s, [ key ]: v } : s ) );
			setAttributes( { stats: next } );
		};
		return (
			<>
				<InspectorControls>
					<PanelBody title="Layout" initialOpen>
						<SelectControl label="Background" value={ a.background } options={ [ { label: 'Cream', value: 'cream' }, { label: 'White', value: 'white' }, { label: 'Sage mist', value: 'sage-mist' } ] } onChange={ set( 'background' ) } />
						<SelectControl label="Image side" value={ a.imageSide } options={ [ { label: 'Left', value: 'left' }, { label: 'Right', value: 'right' } ] } onChange={ set( 'imageSide' ) } />
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { imageUrl: m.url, imageAlt: m.alt || '' } ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ a.imageUrl ? 'Replace image' : 'Pick image' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="Image URL" value={ a.imageUrl } onChange={ set( 'imageUrl' ) } />
						<TextControl label="Image alt" value={ a.imageAlt } onChange={ set( 'imageAlt' ) } />
					</PanelBody>
					<PanelBody title="Extras" initialOpen={ false }>
						<TextareaControl label="Chips (one per line)" value={ ( a.chips || [] ).join( '\n' ) } onChange={ ( v ) => setAttributes( { chips: v.split( '\n' ).filter( ( s ) => s.trim() !== '' ) } ) } rows={ 4 } />
						<TextareaControl label="Gem list (one per line)" value={ ( a.gemItems || [] ).join( '\n' ) } onChange={ ( v ) => setAttributes( { gemItems: v.split( '\n' ).filter( ( s ) => s.trim() !== '' ) } ) } rows={ 4 } />
						{ stats.map( ( s, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', marginBottom: '0.75rem', paddingBottom: '0.5rem' } }>
								<TextControl label={ `Stat ${ i + 1 } value` } value={ s.v } onChange={ setStat( i, 'v' ) } />
								<TextControl label={ `Stat ${ i + 1 } label` } value={ s.k } onChange={ setStat( i, 'k' ) } />
								<Button isDestructive variant="link" onClick={ () => setAttributes( { stats: stats.filter( ( _, j ) => j !== i ) } ) }>Remove stat</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ () => setAttributes( { stats: [ ...stats, { v: '', k: '' } ] } ) }>Add stat</Button>
					</PanelBody>
					<PanelBody title="CTA" initialOpen={ false }>
						<TextControl label="Button text" value={ a.primaryText } onChange={ set( 'primaryText' ) } />
						<TextControl label="Button URL" value={ a.primaryUrl } onChange={ set( 'primaryUrl' ) } />
						<TextControl label="Phone text" value={ a.phoneText } onChange={ set( 'phoneText' ) } />
						<TextControl label="Phone href" value={ a.phoneHref } onChange={ set( 'phoneHref' ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-feature-split rehab-bg-${ a.background }${ a.imageSide === 'right' ? ' rehab-feature-split--image-right' : '' }` }>
						<div className="rehab-container">
							<div className="rehab-feature-split__grid">
								<div className="rehab-feature-split__media">
									{ a.imageUrl ? <img src={ a.imageUrl } alt={ a.imageAlt } /> : <div className="rehab-feature-split__placeholder"><span>{ a.imageAlt || 'Image' }</span></div> }
								</div>
								<div className="rehab-feature-split__copy">
									<RichText tagName="span" className="rehab-feature-split__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
									<RichText tagName="h2" className="rehab-feature-split__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
									<RichText tagName="p" value={ a.body } onChange={ set( 'body' ) } placeholder="Body (blank line between paragraphs)…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
									{ ( a.chips || [] ).length ? (
										<div className="rehab-feature-split__chips">{ a.chips.map( ( c, i ) => <span key={ i }>{ c }</span> ) }</div>
									) : null }
									{ a.quote ? (
										<blockquote className="rehab-feature-split__quote">
											<RichText tagName="p" value={ a.quote } onChange={ set( 'quote' ) } allowedFormats={ [] } />
											<RichText tagName="cite" value={ a.quoteSrc } onChange={ set( 'quoteSrc' ) } placeholder="Source…" allowedFormats={ [] } />
										</blockquote>
									) : (
										<RichText tagName="p" className="rehab-feature-split__quote-placeholder" value={ a.quote } onChange={ set( 'quote' ) } placeholder="Optional pull-quote…" allowedFormats={ [] } />
									) }
									{ stats.length ? (
										<div className="rehab-feature-split__stats">
											{ stats.map( ( s, i ) => (
												<div className="rehab-feature-split__stat" key={ i }><div className="v">{ s.v }</div><div className="k">{ s.k }</div></div>
											) ) }
										</div>
									) : null }
									{ ( a.gemItems || [] ).length ? (
										<div className="rehab-feature-split__gems">{ a.gemItems.map( ( g, i ) => <div className="rehab-feature-split__gem" key={ i }><span>◆</span>{ g }</div> ) }</div>
									) : null }
									<RichText tagName="p" value={ a.footnote } onChange={ set( 'footnote' ) } placeholder="Optional footnote…" allowedFormats={ [] } />
								</div>
							</div>
						</div>
					</section>
				</div>
			</>
		);
	},
	save: () => null,
} );
