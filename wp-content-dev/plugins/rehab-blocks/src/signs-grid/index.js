import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl, TextControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

const Check = () => (
	<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
);

const Card = ( { iconKind, title, items, onTitle, onItems, isEditor } ) => {
	const icon1 = ( <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg> );
	const icon2 = ( <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><path d="M3 12h4l3-9 4 18 3-9h4"/></svg> );
	return (
		<div className="rehab-signs-card">
			<div className="rehab-signs-card__head">
				<div className="rehab-signs-card__icon" aria-hidden="true">{ iconKind === 1 ? icon1 : icon2 }</div>
				{ isEditor ? (
					<RichText tagName="h3" className="rehab-signs-card__title" value={ title } onChange={ onTitle } placeholder="Card title" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
				) : <RichText.Content tagName="h3" className="rehab-signs-card__title" value={ title } /> }
			</div>
			<ul className="rehab-signs-card__list">
				{ items.map( ( item, i ) => (
					<li key={ i }>
						<Check />
						{ isEditor ? (
							<>
								<RichText tagName="span" value={ item } onChange={ ( v ) => { const next = items.slice(); next[ i ] = v; onItems( next ); } } placeholder="Item…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
								<Button
									className="rehab-signs-card__remove"
									icon="trash"
									label="Remove item"
									showTooltip
									isDestructive
									size="small"
									style={ { marginLeft: 'auto', flex: 'none' } }
									onClick={ () => onItems( items.filter( ( _, j ) => j !== i ) ) }
								/>
							</>
						) : <RichText.Content tagName="span" value={ item } /> }
					</li>
				) ) }
			</ul>
		</div>
	);
};

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const items1 = Array.isArray( a.card1Items ) ? a.card1Items : [];
		const items2 = Array.isArray( a.card2Items ) ? a.card2Items : [];
		return (
			<>
				<InspectorControls>
					<PanelBody title="Section" initialOpen>
						<SelectControl label="Background" value={ a.background } options={ [ { label: 'Cream', value: 'cream' }, { label: 'White', value: 'white' }, { label: 'Sage mist', value: 'sage-mist' } ] } onChange={ ( v ) => setAttributes( { background: v } ) } />
					</PanelBody>
					<PanelBody title="List items">
						<Button variant="secondary" onClick={ () => setAttributes( { card1Items: [ ...items1, '' ] } ) } style={ { marginBottom: '0.5rem' } }>Add to card 1</Button>
						<br />
						<Button variant="secondary" onClick={ () => setAttributes( { card2Items: [ ...items2, '' ] } ) }>Add to card 2</Button>
					</PanelBody>
					<PanelBody title="Dark CTA bar" initialOpen={ false }>
						<ToggleControl label="Show CTA bar" checked={ a.showCta } onChange={ ( v ) => setAttributes( { showCta: v } ) } />
						<TextControl label="Button URL" value={ a.ctaUrl } onChange={ ( v ) => setAttributes( { ctaUrl: v } ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-signs-grid rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<div className="rehab-signs-grid__head">
								<RichText tagName="span" className="rehab-signs-grid__eyebrow" value={ a.eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Eyebrow…" allowedFormats={ [] } />
								<RichText tagName="h2" className="rehab-signs-grid__heading" value={ a.heading } onChange={ ( v ) => setAttributes( { heading: v } ) } placeholder="Section heading…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
								<RichText tagName="p" className="rehab-signs-grid__sub" value={ a.subheading } onChange={ ( v ) => setAttributes( { subheading: v } ) } placeholder="Subheading…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
							</div>
							<div className="rehab-signs-grid__cards">
								<Card iconKind={ 1 } title={ a.card1Title } items={ items1 } onTitle={ ( v ) => setAttributes( { card1Title: v } ) } onItems={ ( v ) => setAttributes( { card1Items: v } ) } isEditor />
								<Card iconKind={ 2 } title={ a.card2Title } items={ items2 } onTitle={ ( v ) => setAttributes( { card2Title: v } ) } onItems={ ( v ) => setAttributes( { card2Items: v } ) } isEditor />
							</div>
							{ a.showCta ? (
								<div className="rehab-signs-grid__cta">
									<div className="rehab-signs-grid__cta-text">
										<RichText tagName="h3" value={ a.ctaTitle } onChange={ ( v ) => setAttributes( { ctaTitle: v } ) } placeholder="CTA title" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
										<RichText tagName="p" value={ a.ctaBody } onChange={ ( v ) => setAttributes( { ctaBody: v } ) } placeholder="CTA body…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
									</div>
									<span className="rehab-btn rehab-btn--luxury">
										<RichText tagName="span" value={ a.ctaButton } onChange={ ( v ) => setAttributes( { ctaButton: v } ) } placeholder="Button label" allowedFormats={ [] } />
									</span>
								</div>
							) : null }
						</div>
					</section>
				</div>
			</>
		);
	},
	save( { attributes } ) {
		const a = attributes;
		const items1 = Array.isArray( a.card1Items ) ? a.card1Items : [];
		const items2 = Array.isArray( a.card2Items ) ? a.card2Items : [];
		return (
			<section className={ `rehab-signs-grid rehab-bg-${ a.background }` }>
				<div className="rehab-container">
					<div className="rehab-signs-grid__head">
						<RichText.Content tagName="span" className="rehab-signs-grid__eyebrow" value={ a.eyebrow } />
						<RichText.Content tagName="h2" className="rehab-signs-grid__heading" value={ a.heading } />
						<RichText.Content tagName="p" className="rehab-signs-grid__sub" value={ a.subheading } />
					</div>
					<div className="rehab-signs-grid__cards">
						<Card iconKind={ 1 } title={ a.card1Title } items={ items1 } />
						<Card iconKind={ 2 } title={ a.card2Title } items={ items2 } />
					</div>
					{ a.showCta ? (
						<div className="rehab-signs-grid__cta">
							<div className="rehab-signs-grid__cta-text">
								<RichText.Content tagName="h3" value={ a.ctaTitle } />
								<RichText.Content tagName="p" value={ a.ctaBody } />
							</div>
							<a href={ a.ctaUrl } className="rehab-btn rehab-btn--luxury">
								<RichText.Content value={ a.ctaButton } />
							</a>
						</div>
					) : null }
				</div>
			</section>
		);
	},
} );
