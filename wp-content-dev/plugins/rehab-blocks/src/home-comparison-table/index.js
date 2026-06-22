import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const rows = a.rows || [];
		const setRow = ( i, key ) => ( v ) => {
			const next = rows.map( ( r, idx ) => ( idx === i ? { ...r, [ key ]: v } : r ) );
			setAttributes( { rows: next } );
		};
		const addRow = () =>
			setAttributes( {
				rows: [
					...rows,
					{ feature: '', western: '', diamond: '', highlight: false, key: false, hasPhone: false },
				],
			} );
		const removeRow = ( i ) => setAttributes( { rows: rows.filter( ( _, j ) => j !== i ) } );

		const blockProps = useBlockProps( { className: 'drt-comparison drt-bg-white drt-section--lg' } );

		return (
			<>
				<InspectorControls>
					<PanelBody title="Column headers & labels" initialOpen>
						<TextControl label="Column 1 (feature)" value={ a.colFeature } onChange={ set( 'colFeature' ) } />
						<TextControl label="Column 2 (western)" value={ a.colWestern } onChange={ set( 'colWestern' ) } />
						<TextControl label="Column 3 (diamond)" value={ a.colDiamond } onChange={ set( 'colDiamond' ) } />
						<TextControl label="Mobile card label — western" value={ a.cardWesternLabel } onChange={ set( 'cardWesternLabel' ) } />
						<TextControl label="Mobile card label — diamond" value={ a.cardDiamondLabel } onChange={ set( 'cardDiamondLabel' ) } />
						<TextControl label="Call link text" value={ a.callText } onChange={ set( 'callText' ) } />
						<TextControl label="Call link href (tel:)" value={ a.phoneHref } onChange={ set( 'phoneHref' ) } />
					</PanelBody>
					<PanelBody title="Comparison rows" initialOpen={ false }>
						{ rows.map( ( r, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', marginBottom: '0.75rem', paddingBottom: '0.5rem' } }>
								<TextControl label={ `Row ${ i + 1 } — feature` } value={ r.feature || '' } onChange={ setRow( i, 'feature' ) } />
								<TextControl label="Western value" value={ r.western || '' } onChange={ setRow( i, 'western' ) } />
								<TextControl label="Diamond value" value={ r.diamond || '' } onChange={ setRow( i, 'diamond' ) } />
								<ToggleControl label="Highlight diamond cell" checked={ !! r.highlight } onChange={ setRow( i, 'highlight' ) } />
								<ToggleControl label="Key row (shown on mobile cards)" checked={ !! r.key } onChange={ setRow( i, 'key' ) } />
								<ToggleControl label="Show call-for-pricing link" checked={ !! r.hasPhone } onChange={ setRow( i, 'hasPhone' ) } />
								<Button isDestructive variant="link" onClick={ () => removeRow( i ) }>Remove row</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ addRow }>Add row</Button>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container">
						<RichText tagName="span" className="drt-eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow (optional)…" allowedFormats={ [] } />
						<h2 className="drt-heading drt-heading--lg drt-comparison__title drt-text-balance">
							<RichText tagName="span" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
							<br className="drt-comparison__br-sm" />
							<RichText tagName="span" value={ a.headingEmphasis } onChange={ set( 'headingEmphasis' ) } placeholder="Emphasis line…" allowedFormats={ [] } />
						</h2>
						<RichText tagName="p" className="drt-comparison__intro" value={ a.intro } onChange={ set( 'intro' ) } placeholder="Intro (optional)…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
						<div className="drt-comparison__desktop">
							<table className="drt-comparison__table">
								<thead>
									<tr>
										<th className="drt-comparison__th">{ a.colFeature || 'Feature' }</th>
										<th className="drt-comparison__th">{ a.colWestern || 'Western' }</th>
										<th className="drt-comparison__th drt-comparison__th--diamond">{ a.colDiamond || 'Diamond' }</th>
									</tr>
								</thead>
								<tbody>
									{ rows.map( ( r, i ) => (
										<tr className="drt-comparison__row" key={ i }>
											<td className="drt-comparison__td">{ r.feature }</td>
											<td className="drt-comparison__td">{ r.western }</td>
											<td className={ `drt-comparison__td drt-comparison__td--diamond${ r.highlight ? ' drt-comparison__td--highlight' : '' }` }>{ r.diamond }</td>
										</tr>
									) ) }
								</tbody>
							</table>
						</div>
						<RichText tagName="p" className="drt-comparison__footer" value={ a.footnote } onChange={ set( 'footnote' ) } placeholder="Footnote…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
